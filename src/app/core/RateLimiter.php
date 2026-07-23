<?php

require_once __DIR__ . '/Database.php';

class RateLimiter extends Database
{
    private static $trustedIps = null;

    public static function getClientIp()
    {
        $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];

        foreach ($headers as $header) {
            if (empty($_SERVER[$header])) {
                continue;
            }

            $value = $_SERVER[$header];
            if ($header === 'HTTP_X_FORWARDED_FOR') {
                $value = trim(explode(',', $value)[0]);
            }

            if (filter_var($value, FILTER_VALIDATE_IP)) {
                return $value;
            }
        }

        return 'unknown';
    }

    public function hit($bucket, $actor, $maxRequests, $windowSeconds, $blockSeconds = 0)
    {
        $bucket = substr(trim((string) $bucket), 0, 100);
        $actor = substr(trim((string) $actor), 0, 191);
        $maxRequests = (int) $maxRequests;
        $windowSeconds = (int) $windowSeconds;
        $blockSeconds = (int) $blockSeconds;

        if (empty($bucket) || empty($actor) || $maxRequests < 1 || $windowSeconds < 1 || $this->isTrusted($actor)) {
            return ['allowed' => true, 'remaining' => 0, 'retry_after' => 0];
        }

        try {
            $now = time();
            $windowStart = (int) (floor($now / $windowSeconds) * $windowSeconds);

            $this->prepare('SELECT `blocked_until` FROM `rate_limit_blocks` WHERE `bucket` = ? AND `actor` = ? LIMIT 1');
            $this->statement->execute([$bucket, $actor]);
            $block = $this->statement->fetch(PDO::FETCH_ASSOC);

            if ($block && (int) $block['blocked_until'] > $now) {
                return [
                    'allowed' => false,
                    'remaining' => 0,
                    'retry_after' => (int) $block['blocked_until'] - $now
                ];
            }

            if ($block) {
                $this->prepare('DELETE FROM `rate_limit_blocks` WHERE `bucket` = ? AND `actor` = ?');
                $this->statement->execute([$bucket, $actor]);
            }

            $this->prepare(
                'INSERT INTO `rate_limit_counters` (`bucket`, `actor`, `window_start`, `request_count`) '
                . 'VALUES (?, ?, ?, 1) ON DUPLICATE KEY UPDATE `request_count` = `request_count` + 1'
            );
            $this->statement->execute([$bucket, $actor, $windowStart]);

            $this->prepare('SELECT `request_count` FROM `rate_limit_counters` WHERE `bucket` = ? AND `actor` = ? AND `window_start` = ?');
            $this->statement->execute([$bucket, $actor, $windowStart]);
            $count = (int) $this->statement->fetchColumn();

            if ($count > $maxRequests) {
                $retryAfter = max(1, ($windowStart + $windowSeconds) - $now);

                if ($blockSeconds > 0) {
                    $blockedUntil = $now + $blockSeconds;
                    $this->prepare(
                        'INSERT INTO `rate_limit_blocks` (`bucket`, `actor`, `blocked_until`) VALUES (?, ?, ?) '
                        . 'ON DUPLICATE KEY UPDATE `blocked_until` = GREATEST(`blocked_until`, VALUES(`blocked_until`))'
                    );
                    $this->statement->execute([$bucket, $actor, $blockedUntil]);
                    $retryAfter = max($retryAfter, $blockSeconds);
                }

                return ['allowed' => false, 'remaining' => 0, 'retry_after' => $retryAfter];
            }

            $this->cleanup($now);
            return [
                'allowed' => true,
                'remaining' => max(0, $maxRequests - $count),
                'retry_after' => 0
            ];
        } catch (Exception $e) {
            if (!($e instanceof PDOException && isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1146)) {
                error_log('Rate limiter error: ' . $e->getMessage());
            }
            return ['allowed' => true, 'remaining' => 0, 'retry_after' => 0];
        }
    }

    private function isTrusted($actor)
    {
        $parts = explode('|', $actor);
        $ip = trim((string) end($parts));

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        if (self::$trustedIps === null) {
            self::$trustedIps = ['127.0.0.1' => true, '::1' => true];

            if (!empty($_SERVER['SERVER_ADDR']) && filter_var($_SERVER['SERVER_ADDR'], FILTER_VALIDATE_IP)) {
                self::$trustedIps[$_SERVER['SERVER_ADDR']] = true;
            }

            try {
                $this->prepare('SELECT `ip` FROM `ip_whitelist`');
                $this->statement->execute();
                foreach ($this->statement->fetchAll(PDO::FETCH_COLUMN) as $trustedIp) {
                    self::$trustedIps[$trustedIp] = true;
                }
            } catch (Exception $e) {
            }
        }

        return isset(self::$trustedIps[$ip]);
    }

    private function cleanup($now)
    {
        static $hits = 0;
        $hits++;

        if (($hits % 100) !== 0) {
            return;
        }

        $this->prepare('DELETE FROM `rate_limit_counters` WHERE `window_start` < ?');
        $this->statement->execute([$now - 86400]);
        $this->prepare('DELETE FROM `rate_limit_blocks` WHERE `blocked_until` < ?');
        $this->statement->execute([$now]);
    }
}
