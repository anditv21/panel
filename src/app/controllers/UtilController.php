<?php

// Only Public methods

require_once SITE_ROOT . "/app/models/UtilModel.php";
require_once SITE_ROOT . "/app/helpers/set_timezone.php";
class Util extends UtilMod
{
    public const LIFETIME_SUBSCRIPTION_DAYS = 1000;
    public const REMEMBER_TOKEN_LIFETIME_DAYS = 30;

    public static function isHttpsRequest()
    {
        if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
            return true;
        }

        return !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
    }

    public static function getRememberTokenExpiry($createdAt)
    {
        $createdDate = DateTime::createFromFormat('Y-m-d', (string) $createdAt);
        if (!$createdDate) {
            return false;
        }

        $createdDate->setTime(23, 59, 59);
        $createdDate->modify('+' . self::REMEMBER_TOKEN_LIFETIME_DAYS . ' days');
        return $createdDate->getTimestamp();
    }

    public static function setLoginCookie($token, $expiresAt)
    {
        setcookie('login_cookie', $token, [
            'expires' => (int) $expiresAt,
            'path' => '/',
            'secure' => self::isHttpsRequest(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public static function clearLoginCookie()
    {
        setcookie('login_cookie', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => self::isHttpsRequest(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public function setPageTitle($title)
    {
        if (!empty(Session::get("username"))) {
            $title = Util::securevar(Session::get("username")) . ' &ndash; ' . $title;
            Util::display("<title>$title</title>");
        } else {
            $title =  $title . ' &ndash; ' . SITE_NAME;
            Util::display("<title>$title</title>");
        }
    }

    public static function redirect($location)
    {
        header('location:' . SUB_DIR . $location);
        exit();
    }

    public static function head($title)
    {
        $util = new Util();
        $util->setPageTitle($title);
        include SITE_ROOT . '/includes/head.inc.php';
    }

    public static function navbar()
    {
        include SITE_ROOT . '/includes/navbar.inc.php';
    }

    public static function adminNavbar()
    {
        include(SITE_ROOT . '/includes/navbar.inc.php');
    }


    public static function footer()
    {
        include SITE_ROOT . '/includes/footer.inc.php';
    }

    public static function display($string)
    {
        echo $string;
    }

    public static function securevar($var)
    {
        if (empty($var)) {
            return $var;
        }
        if (is_array($var)) {
            $new_array = array();
            foreach ($var as $key => $value) {
                if (is_string($value)) {
                    $new_array[$key] = htmlspecialchars(stripslashes(trim($value)));
                } elseif (is_array($value)) {
                    $new_array[$key] = self::securevar($value);
                } else {
                    $new_array[$key] = $value;
                }
            }
            return $new_array;
        } else {
            return htmlspecialchars(stripslashes(trim($var)));
        }
    }

    public static function checktoken()
    {
        if (isset($_COOKIE['login_cookie'])) {
            $token = Util::securevar($_COOKIE['login_cookie']);

            $util = new UtilMod();
            $result = $util->validateRememberToken($token);

            if ($result) {
                self::csrfCheck();
            }

            if ($result && !self::has2faSolved() && basename($_SERVER['PHP_SELF']) !== '2fa.php') {
                Util::redirect('/auth/2fa.php');
            }

            return $result;
        }

        Util::clearLoginCookie();
        Session::destroy();
        Util::redirect('/auth/login.php');
    }

    public static function csrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function csrfField()
    {
        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function csrfCheck()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $token = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';

        if (empty($_SESSION['csrf_token']) || empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            exit('Invalid request.');
        }
    }

    public static function has2faSolved($token = null)
    {
        if (empty($token)) {
            if (!isset($_COOKIE['login_cookie'])) {
                return false;
            }

            $token = Util::securevar($_COOKIE['login_cookie']);
        }

        $util = new UtilMod();
        return $util->check2faSolved($token);
    }


    // Returns random string
    public static function randomCode($int)
    {
        $characters =
            '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $int; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }


    public function getSubStatus()
    {
        // Bind data
        $username = Session::get('username');
        return $this->subActiveCheck($username);
    }

    public static function isLifetimeSubscription($days)
    {
        return is_numeric($days) && (int) $days >= self::LIFETIME_SUBSCRIPTION_DAYS;
    }

    public static function formatSubscriptionLabel($days, $lifetimeLabel = 'Lifetime')
    {
        return self::isLifetimeSubscription($days) ? $lifetimeLabel : $days;
    }

    // admin check
    public static function adminCheck($redirect = true)
    {
        $util = new UtilMod();
        $res = $util->checkadmin(Session::get("username"));
        if ($res != true) {
            if ($redirect && basename($_SERVER['PHP_SELF']) != 'index.php') {
                Session::set("admin", (int) 0);
                Util::redirect('/index.php');
                exit(); // to prevent infinite loop
            }
        } else {
            Session::set("admin", (int) 1);
            return true;
        }
    }

    // supp check
    public static function suppCheck($redirect = true)
    {
        $util = new UtilMod();
        $res = $util->checksupp(Session::get("username"));
        if ($res != true) {
            if ($redirect && basename($_SERVER['PHP_SELF']) != 'index.php') {
                Session::set("supp", (int) 0);
                Util::redirect('/index.php');
                exit(); // to prevent infinite loop
            }
        } else {
            Session::set("supp", (int) 1);
            return true;
        }
    }


    // ban check
    public static function banCheck($redirect = true)
    {
        $util = new UtilMod();
        $res = $util->checkBan(Session::get("username"));
        if ($res == true) {
            if ($redirect && basename($_SERVER['PHP_SELF']) !== 'banned.php') {
                Session::set("banned", 1);
                Util::redirect('/banned.php');
                exit(); // to prevent infinite loop
            }
            Session::set("banned", 1);
            return true;
        } else {
            Session::set("banned", 0);
            return false;
        }
    }

    public static function muteCheck()
    {
        $username = Session::get("username");
        if ($username === null) {
            error_log("user not found");
            return false;
        }

        $util = new UtilMod();
        $res = $util->checkmute($username);
        return $res;
    }

    public static function calculate_days($date)
    {
        $now = new DateTime();
        $date = new DateTime($date);
        $interval = $now->diff($date);

        return (int) $interval->format("%a");
    }

    public static function calculate_cooldown($date)
    {
        $now = new DateTime();
        $date = new DateTime($date);

        if ($date < $now) {
            return 0;
        }

        $interval = $now->diff($date);
        return (int) $interval->format("%a");
    }

    public static function getavatar($uid)
    {
        $extension = self::getAvatarExtension($uid);
        if ($extension === false) {
            return false;
        }

        return IMG_URL . $uid . "." . $extension . "?" . Util::randomCode(5);
    }

    public static function getavatardl($uid)
    {
        $extension = self::getAvatarExtension($uid);
        if ($extension === false) {
            return false;
        }

        return IMG_URL . $uid . "." . $extension;
    }

    public static function getextention($uid)
    {
        $extension = self::getAvatarExtension($uid);
        return $extension === false ? false : "." . $extension;
    }

    public static function saveAvatarData($imageData, $uid)
    {
        if (@getimagesizefromstring($imageData) === false) {
            return false;
        }

        $extension = self::detectAvatarExtension($imageData);
        if ($extension === false) {
            return false;
        }

        $path = IMG_DIR . $uid;
        $temporaryFile = $path . ".tmp." . $extension;

        if (file_put_contents($temporaryFile, $imageData) === false || filesize($temporaryFile) < 1) {
            @unlink($temporaryFile);
            return false;
        }

        $avatarFile = $path . "." . $extension;
        if (!@copy($temporaryFile, $avatarFile)) {
            @unlink($temporaryFile);
            return false;
        }
        @unlink($temporaryFile);

        foreach (['webp', 'png', 'jpg', 'gif'] as $oldExtension) {
            if ($oldExtension !== $extension) {
                @unlink($path . "." . $oldExtension);
            }
        }

        @chmod(IMG_DIR, 0775);
        @chmod($avatarFile, 0775);
        return true;
    }

    private static function getAvatarExtension($uid)
    {
        $path = IMG_DIR . $uid;

        foreach (['webp', 'png', 'jpg', 'gif'] as $extension) {
            if (@getimagesize($path . "." . $extension)) {
                return $extension;
            }
        }

        return false;
    }

    private static function detectAvatarExtension($imageData)
    {
        if (substr($imageData, 0, 4) === 'RIFF' && substr($imageData, 8, 4) === 'WEBP') {
            return 'webp';
        }
        if (substr($imageData, 0, 6) === 'GIF87a' || substr($imageData, 0, 6) === 'GIF89a') {
            return 'gif';
        }
        if (substr($imageData, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A") {
            return 'png';
        }
        if (substr($imageData, 0, 3) === "\xFF\xD8\xFF") {
            return 'jpg';
        }

        return false;
    }

    public static function daysago($dateString)
    {
        if (!$dateString) {
            return 'Not available';
        }

        $date = strtotime($dateString);
        if ($date === false) {
            return 'Not available';
        }

        $now = time();
        $diff = $now - $date;

        if ($diff < 0) {
            return 'In the future';
        }

        $days = floor($diff / (60 * 60 * 24));

        if ($days == 0) {
            return 'Today';
        } elseif ($days == 1) {
            return 'Yesterday';
        } elseif ($days < 30) {
            return $days . ' days ago';
        }

        $months = floor($days / 30);
        if ($days < 365) {
            return $months . ' month' . ($months == 1 ? '' : 's') . ' ago';
        }

        $years = floor($days / 365);
        return $years . ' year' . ($years == 1 ? '' : 's') . ' ago';
    }
}
