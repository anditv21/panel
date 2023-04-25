<?php

// Only Public methods

require_once SITE_ROOT . "/app/models/UtilModel.php";

class Util extends UtilMod
{

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
        $util = new Util;
        $util->setPageTitle($title);
        include SITE_ROOT . '/includes/head.inc.php';
    }

    public static function navbar()
    {
        include SITE_ROOT . '/includes/navbar.inc.php';
    }

    public static function adminNavbar()
    {
        include(SITE_ROOT . '/admin/includes/adminNavbar.inc.php');
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
                } else if (is_array($value)) {
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
            return $result;
        }
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

    // admin check
    public static function adminCheck()
    {
        $util = new UtilMod();
        $res = $util->checkadmin(Session::get("username"));
        if ($res != true) {
            if (basename($_SERVER['PHP_SELF']) != 'index.php') {
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
    public static function suppCheck()
    {
        $util = new UtilMod();
        $res = $util->checksupp(Session::get("username"));
        if ($res != true) {
            if (basename($_SERVER['PHP_SELF']) != 'index.php') {
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
    public static function banCheck()
    {
        $util = new UtilMod();
        $res = $util->checkban(Session::get("username"));

        // If user is banned
        if ($res == true) {
            if (basename($_SERVER['PHP_SELF']) != 'banned.php') {
                Session::set("banned", (int) 1);
                Util::redirect('/banned.php');
                exit(); // to prevent infinite loop
            }
        } else {
            Session::set("banned", (int) 0);
            return false;
        }
    }

    public static function getjoin()
    {
        $joindate = Session::get("createdAt");
        $now = new DateTime();
        $date = new DateTime($joindate);
        $interval = $now->diff($date);

        return (int) $interval->format("%a");
    }

    public static function getjoinprofile($joindate)
    {
        $now = new DateTime();
        $date = DateTime::createFromFormat("Y-m-d H:i:s", $joindate);
        $interval = $now->diff($date);

        return (int) $interval->format("%a");
    }

    public static function getavatar($uid)
    {
        $path = IMG_DIR . $uid;
        if (@getimagesize($path . ".png")) {
            return IMG_URL . $uid . ".png?" . Util::randomCode(5);
        } elseif (@getimagesize($path . ".jpg")) {
            return IMG_URL . $uid . ".jpg?" . Util::randomCode(5);
        } elseif (@getimagesize($path . ".gif")) {
            return IMG_URL . $uid . ".gif?" . Util::randomCode(5);
        } else {
            return false;
        }
    }

    public static function getavatardl($uid)
    {
        $path = IMG_DIR . $uid;
        if (@getimagesize($path . ".png")) {
            return IMG_URL . $uid . ".png";
        } elseif (@getimagesize($path . ".jpg")) {
            return IMG_URL . $uid . ".jpg";
        } elseif (@getimagesize($path . ".gif")) {
            return IMG_URL . $uid . ".gif";
        } else {
            return false;
        }
    }

    public static function getextention($uid)
    {
        $path = IMG_DIR . $uid;
        if (@getimagesize($path . ".png")) {
            return ".png";
        } elseif (@getimagesize($path . ".jpg")) {
            return  ".jpg";
        } elseif (@getimagesize($path . ".gif")) {
            return  ".gif";
        } else {
            return false;
        }
    }

    public static function daysago($dateString)
    {
        if (!$dateString) {
            return 'Not available';
        }
        $date = strtotime($dateString);
        $now = time();
        $diff = $now - $date;
        $days = floor($diff / (60 * 60 * 24));
        if ($days == 0) {
            return 'Today';
        } else if ($days == 1) {
            return 'Yesterday';
        } else {
            return $days . ' days ago';
        }
    }
}
