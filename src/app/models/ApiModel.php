<?php

// Extends to class Database
// Only Protected methods
// * Interats with all tables *

require_once SITE_ROOT . "/app/core/Database.php";
require_once SITE_ROOT . "/app/require.php";
require_once SITE_ROOT . "/app/helpers/set_timezone.php";
class API extends Database
{
    protected function getSubscriptionDays($subDate)
    {
        if (empty($subDate)) {
            return 0;
        }

        try {
            $currentDate = new DateTime('today');
            $subscriptionDate = new DateTime($subDate);
            return (int) $currentDate->diff($subscriptionDate)->format('%r%a');
        } catch (Exception $e) {
            return 0;
        }
    }

    protected function userAPI($username, $password, $hwid)
    {
        // fetch username
        $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $this->statement->execute([$username]);
        $row = $this->statement->fetch();

        // If username is correct
        if ($row) {
            $hashedPassword = $row->password;

            // If password is correct
            if (password_verify($password, $hashedPassword)) {
                if ($row->hwid === null) {
                    try {
                        $this->prepare("UPDATE `users` SET `hwid` = ? WHERE `username` = ?");
                        $this->statement->execute([$hwid, $username]);
                        $row->hwid = $hwid;
                    } catch (PDOException $e) {
                        if (isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062) {
                            return [
                                "status" => "failed",
                                "error" => "HWID is already linked to another account"
                            ];
                        }

                        throw $e;
                    }
                }

                $uid = $row->uid;
                $path = IMG_DIR . $uid;
                if (@getimagesize($path . ".webp")) {
                    $avatarurl = IMG_URL . $uid . ".webp?" . Util::randomCode(5);
                } elseif (@getimagesize($path . ".png")) {
                    $avatarurl = IMG_URL . $uid . ".png?" . Util::randomCode(5);
                } elseif (@getimagesize($path . ".jpg")) {
                    $avatarurl = IMG_URL . $uid . ".jpg?" . Util::randomCode(5);
                } elseif (@getimagesize($path . ".gif")) {
                    $avatarurl = IMG_URL . $uid . ".gif?" . Util::randomCode(5);
                } else {
                    $avatarurl =
                        SITE_URL .
                        SUB_DIR .
                        "/assets/images/avatars/Portrait_Placeholder.png";
                }

                $this->prepare("SELECT `frozen`, `status`, `version`, `maintenance` FROM `system`");
                $this->statement->execute();
                $res = $this->statement->fetch();

                if (!$res) {
                    error_log("User API could not find the system settings");
                    $res = (object) [
                        "frozen" => 0,
                        "status" => "",
                        "version" => "",
                        "maintenance" => 0
                    ];
                }

                $response = [
                    "status" => "success",
                    "uid" => (int) $row->uid,
                    "username" => $row->username,
                    "displayname" => $row->displayname,
                    "hwid" => is_null($row->hwid) ? "null" : $row->hwid,
                    "admin" => (int) $row->admin,
                    "supp" => (int) $row->supp,
                    "sub" => $this->getSubscriptionDays($row->sub),
                    "banned" => (int) $row->banned,
                    "invitedBy" => $row->invitedBy,
                    "createdAt" => $row->createdAt,
                    "avatarurl" => $avatarurl,
                    "frozen" => $res->frozen,
                    "Systemstatus" => $res->status,
                    "Systemversion" => $res->version,
                    "Systemmaintenance" => $res->maintenance,
                ];
            } else {
                // Wrong pass, user exists
                $response = [
                    "status" => "failed",
                    "error" => "Invalid password",
                ];
            }
        } else {
            // Wrong username, user doesnt exists
            $response = ["status" => "failed", "error" => "Invalid username"];
        }

        return $response;
    }

    protected function getuserbydiscord($dcid)
    {
        try {
            $this->prepare("SELECT `uid`, `username`, `sub` , `displayname`, `banned`, `admin`, `supp` FROM `users` WHERE `dcid` = ?");
            $this->statement->execute([$dcid]);
            $result = $this->statement->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $response = [
                    "status" => "failed",
                    "error" => "No user with the provided discord id was found"
                ];
            } else {
                $uid = $result['uid'];
                $path = IMG_DIR . $uid;
                if (@getimagesize($path . ".webp")) {
                    $avatarurl = IMG_URL . $uid . ".webp?" . Util::randomCode(5);
                } elseif (@getimagesize($path . ".png")) {
                    $avatarurl = IMG_URL . $uid . ".png?" . Util::randomCode(5);
                } elseif (@getimagesize($path . ".jpg")) {
                    $avatarurl = IMG_URL . $uid . ".jpg?" . Util::randomCode(5);
                } elseif (@getimagesize($path . ".gif")) {
                    $avatarurl = IMG_URL . $uid . ".gif?" . Util::randomCode(5);
                } else {
                    $avatarurl =
                        SITE_URL .
                        SUB_DIR .
                        "/assets/images/avatars/Portrait_Placeholder.png";
                }


                $uid = $result['uid'] ?? '';
                $username = $result['username'] ?? '';
                $displayname = $result['displayname'] ?? '';
                $banned = $result['banned'] ?? '';
                $admin = $result['admin'] ?? '';
                $supp = $result['supp'] ?? '';
                $sub = $result['sub'] ?? '';

                $response = [
                    "uid" => $uid,
                    "username" => $username,
                    "display_name" => $displayname,
                    "banned" => $banned,
                    "admin" => $admin,
                    "supp" => $supp,
                    "avatar_url" => $avatarurl,
                    "sub" => $sub
                ];
            }
        } catch (Exception $e) {
            $response = [
                "status" => "failed",
                "error" => $e->getMessage()
            ];
        }
        return $response;
    }

    protected function count_users()
    {
        try {
            $this->prepare("SELECT COUNT(*) FROM `users`");
            $this->statement->execute();
            $usercount = (int) $this->statement->fetchColumn();
            $response = [
                "status" => "success",
                "text" => $usercount
            ];
        } catch (Exception $e) {
            $response = [
                "status" => "failed",
                "error" => $e->getMessage()
            ];
        }
        return $response;
    }

    protected function get_linked_users()
    {
        try {
            $this->prepare("SELECT `uid`, `username` ,`displayname`, `dcid` FROM `users` WHERE `dcid` IS NOT NULL");
            $this->statement->execute();
            $linked_users = $this->statement->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                "status" => "success",
                "data" => $linked_users
            ];
        } catch (Exception $e) {
            $response = [
                "status" => "failed",
                "error" => $e->getMessage()
            ];
        }
        return $response;
    }

    protected function generate_sub($dcid, $time)
    {
        try {
            $this->prepare("SELECT `username`, `admin` FROM `users` WHERE `dcid` = ?");
            $this->statement->execute([$dcid]);
            $result = $this->statement->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return [
                    "status" => "failed",
                    "error" => "No user with the provided Discord ID was found"
                ];
            }

            if (empty($result["admin"])) {
                return [
                    "status" => "failed",
                    "error" => "You don't have the necessary permissions to perform this action."
                ];
            }

            $code = "$time-" . Util::randomCode(20);
            $this->prepare('INSERT INTO `subscription` (`code`, `createdBy`) VALUES (?, ?)');
            $this->statement->execute([$code, $result["username"]]);
            $user = new UserController();
            $user->log($result["username"], "Generated a sub", 'admin_logs');

            $response = [
                "status" => "success",
                "text" => $code
            ];
        } catch (Exception $e) {
            error_log("Bot subscription generation failed: " . $e->getMessage());
            $response = [
                "status" => "failed",
                "error" => "Subscription generation failed"
            ];
        }
        return $response;
    }

    protected function generate_inv($dcid)
    {
        try {
            $this->prepare("SELECT `username`, `admin` FROM `users` WHERE `dcid` = ?");
            $this->statement->execute([$dcid]);
            $result = $this->statement->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return [
                    "status" => "failed",
                    "error" => "No user with the provided Discord ID was found"
                ];
            }

            if (empty($result["admin"])) {
                return [
                    "status" => "failed",
                    "error" => "You don't have the necessary permissions to perform this action."
                ];
            }

            $code = Util::randomCode(20);
            $this->prepare('INSERT INTO `invites` (`code`, `createdBy`) VALUES (?, ?)');
            $this->statement->execute([$code, $result["username"]]);
            $user = new UserController();
            $user->log($result["username"], "Generated an invitation", 'admin_logs');

            $response = [
                "status" => "success",
                "text" => $code
            ];
        } catch (Exception $e) {
            error_log("Bot invitation generation failed: " . $e->getMessage());
            $response = [
                "status" => "failed",
                "error" => "Invitation generation failed"
            ];
        }
        return $response;
    }

    protected function getNotifications()
    {
        try {
            $this->prepare('SELECT `id`, `dcid`, `message`, `time` AS `timestamp`, `delivered` FROM `notifications` WHERE `delivered` != 1 ORDER BY `time` DESC');
            $this->statement->execute();
            $notifications = $this->statement->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'notifications' => $notifications
            ];
        } catch (PDOException $e) {
            return [
                'status' => 'failed',
                'error' => 'Database error',
                'notifications' => []
            ];
        }
    }

    protected function setDelivered($id)
    {
        try {
            $this->prepare('UPDATE `notifications` SET `delivered` = 1 WHERE `id` = ?');
            $this->statement->execute([(int) $id]);
            return ['status' => 'success'];
        } catch (PDOException $e) {
            return ['status' => 'failed', 'error' => 'Database error'];
        }
    }

    protected function getWhitelistedIPs(): array
    {
        try {
            $this->prepare('SELECT `ip` FROM `ip_whitelist`');
            $this->statement->execute();
            $result = $this->statement->fetchAll(PDO::FETCH_COLUMN);
            return $result;
        } catch (PDOException $e) {
            error_log("IP whitelist error: " . $e->getMessage());
            return [];
        }
    }

}
