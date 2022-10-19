<?php

// Extends to class Database
// Only Protected methods
// * Interats with all tables *

require_once SITE_ROOT . "/app/core/Database.php";

class API extends Database
{
  protected function botAPI()
  {
    $response = [
      "status" => "success",
    ];

    return $response;
  }

  protected function banuser($usertoban, $reason)
  {
    $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
    $this->statement->execute([$usertoban]);

    if ($this->statement->rowCount() > 0) {
      $usercheck = true;
    } else {
      $usercheck = false;
    }

    if (!$usercheck) {
      $response = [
        "status" => "failed",
        "error" => "User not found",
      ];
      return $response;
    }

    $this->prepare(
      "UPDATE `users` SET `banned` = 1 WHERE `username` = ?"
    );

    $this->statement->execute([$usertoban]);

    $this->prepare(
        "UPDATE `users` SET `banreason` = ? WHERE `username` = ?"
      );
  
    

    if ($this->statement->execute([$reason, $usertoban])) {
      $response = [
        "status" => "success",
      ];

      return $response;
    } else {
      $response = [
        "status" => "failed",
      ];

      return $response;
    }
  }

  protected function subActiveCheck($username)
  {
    $date = new DateTime(); // Get current date
    $currentDate = $date->format("Y-m-d"); // Format Year-Month-Day

    $this->prepare("SELECT `sub` FROM `users` WHERE `username` = ?");
    $this->statement->execute([$username]);
    $subTime = $this->statement->fetch();

    // Pasted from https://www.w3schools.com/php/phptryit.asp?filename=tryphp_func_date_diff
    $date1 = date_create($currentDate); // Convert String to date format
    $date2 = date_create($subTime->sub); // Convert String to date format
    $diff = date_diff($date1, $date2);
    return intval($diff->format("%R%a"));
  }

  protected function redeemsub($username, $code)
  {
    $this->prepare("SELECT * FROM `subscription` WHERE `code` = ?");
    $this->statement->execute([$code]);

    if ($this->statement->rowCount() > 0) {
      $subcheck = true;
    } else {
      $subcheck = false;
    }

    $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
    $this->statement->execute([$username]);

    if ($this->statement->rowCount() > 0) {
      $usercheck = true;
    } else {
      $usercheck = false;
    }

    if (!$subcheck) {
      $response = [
        "status" => "failed",
        "error" => "Invalid sub code",
      ];
      return $response;
    } elseif (!$usercheck) {
      $response = [
        "status" => "failed",
        "error" => "User not found",
      ];
      return $response;
    } else {
      $word = "3m-";

      // Test if subCode contains the 3 months keyword
      if (strpos($code, $word) !== false) {
        $sub = $this->subActiveCheck($username);

        if ($sub <= 0) {
          $date = new DateTime(); // Get current date
          $date->add(new DateInterval("P90D")); // Adds 90 days
          $subTime = $date->format("Y-m-d"); // Format Year-Month-Day
          $this->prepare("UPDATE `users` SET `sub` = ? WHERE `username` = ?");

          if ($this->statement->execute([$subTime, $username])) {
            // Delete the sub code
            $this->prepare("DELETE FROM `subscription` WHERE `code` = ?");
            $this->statement->execute([$code]);
          } else {
            $response = [
              "status" => "failed",
              "error" => "something went wrong",
            ];
            return $response;
          }
        } else {
          $this->prepare("SELECT sub FROM users WHERE username = ?");
          $this->statement->execute([$username]);
          $date = $this->statement->fetch();
          $date1 = date_create($date->sub);
          $date1->add(new DateInterval("P90D")); // Adds 90 days
          $subTime = $date1->format("Y-m-d"); // Format Year-Month-Day
          $this->prepare("UPDATE users SET sub = ? WHERE  username = ?");
          $this->statement->execute([$subTime, $username]);

          $this->prepare("DELETE FROM `subscription` WHERE `code` = ?");
          $this->statement->execute([$code]);
        }
      }

      $word2 = "Trail-";

      // Test if subCode contains the trail keyword
      if (strpos($code, $word2) !== false) {
        $sub = $this->subActiveCheck($username);

        if ($sub <= 0) {
          $date = new DateTime(); // Get current date
          $date->add(new DateInterval("P3D")); // Adds 3 days
          $subTime = $date->format("Y-m-d"); // Format Year-Month-Day
          $this->prepare("UPDATE `users` SET `sub` = ? WHERE `username` = ?");

          if ($this->statement->execute([$subTime, $username])) {
            // Delete the sub code
            $this->prepare("DELETE FROM `subscription` WHERE `code` = ?");
            $this->statement->execute([$code]);
          } else {
            $response = [
              "status" => "failed",
              "error" => "something went wrong",
            ];
            return $response;
          }
        } else {
          $this->prepare("SELECT sub FROM users WHERE username = ?");
          $this->statement->execute([$username]);
          $date = $this->statement->fetch();
          $date1 = date_create($date->sub);
          $date1->add(new DateInterval("P3D")); // Adds 3 days
          $subTime = $date1->format("Y-m-d"); // Format Year-Month-Day
          $this->prepare("UPDATE users SET sub = ? WHERE  username = ?");
          $this->statement->execute([$subTime, $username]);

          $this->prepare("DELETE FROM `subscription` WHERE `code` = ?");
          $this->statement->execute([$code]);
        }
      } else {
        $sub = $this->subActiveCheck($username);

        if ($sub <= 0) {
          $date = new DateTime(); // Get current date
          $date->add(new DateInterval("P30D")); // Adds 30 days
          $subTime = $date->format("Y-m-d"); // Format Year-Month-Day
          $this->prepare("UPDATE `users` SET `sub` = ? WHERE `username` = ?");

          if ($this->statement->execute([$subTime, $username])) {
            // Delete the sub code
            $this->prepare("DELETE FROM `subscription` WHERE `code` = ?");
            $this->statement->execute([$code]);
            return "Your subscription is now active!";
          } else {
            $response = [
              "status" => "failed",
              "error" => "something went wrong",
            ];
            return $response;
          }
        } else {
          $word = "3m-";

          // Test if subCode contains the 3 months keywoard
          if (strpos($code, $word) !== false) {
          } else {
            $this->prepare("SELECT sub FROM users WHERE username = ?");
            $this->statement->execute([$username]);
            $date = $this->statement->fetch();
            $date1 = date_create($date->sub);
            $date1->add(new DateInterval("P30D")); // Adds 30 days
            $subTime = $date1->format("Y-m-d"); // Format Year-Month-Day
            $this->prepare("UPDATE users SET sub = ? WHERE  username = ?");
            $this->statement->execute([$subTime, $username]);

            $this->prepare("DELETE FROM `subscription` WHERE `code` = ?");
            $this->statement->execute([$code]);
          }
          $response = [
            "status" => "success",
          ];
          return $response;
        }
      }
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
          $this->prepare("UPDATE `users` SET `hwid` = ? WHERE `username` = ?");
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
            SITE_URL . SUB_DIR . "/assets/img/avatars/Portrait_Placeholder.png";
        }

        $this->prepare("SELECT * FROM `cheat`");
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
          "cheatstatus" => $res->status,
          "cheatversion" => $res->version,
          "cheatmaintenance" => $res->aintenance,
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

  protected function statsAPI()
  {
    try {
      $this->prepare("SELECT * FROM `users`");
      $this->statement->execute();
      $usercount = $this->statement->rowCount();

      $this->prepare("SELECT * FROM `users` WHERE `banned` =  1");
      $this->statement->execute();
      $banned = $this->statement->rowCount();

      $this->prepare("SELECT * FROM `users` WHERE `sub` > CURRENT_DATE()");
      $this->statement->execute();
      $sub = $this->statement->rowCount();

      $this->prepare("SELECT `injectcount` FROM `cheat`");
      $this->statement->execute();
      $result = $this->statement->fetch();
      $injectcount = $result->injectcount;
    } catch (Exception $e) {
      $response = [
        "status" => "failed",
        "exception" => $e,
      ];
    }

    $response = [
      "status" => "success",
      "usercount" => $usercount,
      "bannedcount" => $banned,
      "activeusers" => $sub,
      "injects" => $injectcount,
    ];
    return $response;
  }
}
