<?php

// Extends to class Database
// Only Protected methods
// * Interats with all tables *

require_once SITE_ROOT . "/app/core/Database.php";
require_once SITE_ROOT . "/app/require.php";

class API extends Database
{
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
                    $this->prepare(
                        "UPDATE `users` SET `hwid` = ? WHERE `username` = ?"
                    );
                    $this->statement->execute([$hwid, $username]);
                }

                $uid = $row->uid;
                $path = IMG_DIR . $uid;
                if (@getimagesize($path . ".png")) {
                    $avatarurl = IMG_URL . $uid . ".png?" . Util::randomCode(5);
                } elseif (@getimagesize($path . ".jpg")) {
                    $avatarurl = IMG_URL . $uid . ".jpg?" . Util::randomCode(5);
                } elseif (@getimagesize($path . ".gif")) {
                    $avatarurl = IMG_URL . $uid . ".gif?" . Util::randomCode(5);
                } else {
                    $avatarurl =
                        SITE_URL .
                        SUB_DIR .
                        "/assets/img/avatars/Portrait_Placeholder.png";
                }

                $this->prepare("SELECT * FROM `system`");
                $this->statement->execute();
                $res = $this->statement->fetch();

                $response = [
                    "status" => "success",
                    "uid" => $row->uid,
                    "username" => $row->username,
                    "hwid" => $row->hwid,
                    "admin" => $row->admin,
                    "supp" => $row->supp,
                    "sub" => $row->sub,
                    "banned" => $row->banned,
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
                if (@getimagesize($path . ".png")) {
                    $avatarurl = IMG_URL . $uid . ".png?" . Util::randomCode(5);
                } elseif (@getimagesize($path . ".jpg")) {
                    $avatarurl = IMG_URL . $uid . ".jpg?" . Util::randomCode(5);
                } elseif (@getimagesize($path . ".gif")) {
                    $avatarurl = IMG_URL . $uid . ".gif?" . Util::randomCode(5);
                } else {
                    $avatarurl =
                        SITE_URL .
                        SUB_DIR .
                        "/assets/img/avatars/Portrait_Placeholder.png";
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
            $this->prepare("SELECT * FROM `users`");
            $this->statement->execute();
            $usercount = $this->statement->rowCount();
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
            $this->prepare("SELECT * FROM `users` WHERE `dcid` = ?");
            $this->statement->execute([$dcid]);
            $result = $this->statement->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $response = [
                    "status" => "failed",
                    "error" => "No user with the provided Discord ID was found"
                ];
            }

            $code = "$time-" . Util::randomCode(20);
            if ($result["admin"]) {
                $this->prepare('INSERT INTO `subscription` (`code`, `createdBy`) VALUES (?, ?)');
                $this->statement->execute([$code, $result["username"]]);
                $user = new UserController();
                $user->log($result["username"], "Generated a sub", 'admin_logs');

                $response = [
                    "status" => "success",
                    "text" => $code
                ];
            } elseif (empty($result["admin"])) {
                $response = [
                    "status" => "failed",
                    "error" => "No user with the provided Discord ID was found"
                ];
            } else {
                $response = [
                    "status" => "failed",
                    "error" => "You don't have the necessary permissions to perform this action."
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

    protected function generate_inv($dcid)
    {
        try {
            $this->prepare("SELECT * FROM `users` WHERE `dcid` = ?");
            $this->statement->execute([$dcid]);
            $result = $this->statement->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $response = [
                    "status" => "failed",
                    "error" => "No user with the provided Discord ID was found"
                ];
            }

            $code = Util::randomCode(20);
            if ($result["admin"]) {
                $this->prepare('INSERT INTO `invites` (`code`, `createdBy`) VALUES (?, ?)');
                $this->statement->execute([$code, $result["username"]]);
                $user = new UserController();
                $user->log($result["username"], "Generated an invitation", 'admin_logs');

                $response = [
                    "status" => "success",
                    "text" => $code
                ];
            } elseif (empty($result["admin"] || $result["supp"])) {
                $response = [
                    "status" => "failed",
                    "error" => "No user with the provided Discord ID was found"
                ];
            } else {
                $response = [
                    "status" => "failed",
                    "error" => "You don't have the necessary permissions to perform this action."
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

    protected function getWhitelistedIPs(): array
    {
        try {
            $this->prepare('SELECT `ip` FROM `ip_whitelist`');
            $this->statement->execute();
            $result = $this->statement->fetchAll(PDO::FETCH_COLUMN);
            return $result;
        } catch (PDOException $e) {

            echo "Error: " . $e->getMessage();
            return [];
        }
    }

}
