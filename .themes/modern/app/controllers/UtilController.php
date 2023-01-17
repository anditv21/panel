<?php

// Extends to NO classes
// Only Public methods

class Util
{
    public static function redirect($location)
    {
        header('location:' . SUB_DIR . $location);
        exit();
    }

    public static function head($title)
    {
        include SITE_ROOT . '/includes/head.inc.php';
    }

    public static function navbar()
    {
        include SITE_ROOT . '/includes/navbar.inc.php';
    }

    public static function adminNavbar()
    {
        include(SITE_ROOT . '/includes/adminNavbar.inc.php');
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
        if(!empty($var))
        {
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
        else
        {
            return "";
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

    // ban check
    public static function banCheck()
    {
        // If user is banned
        if (Session::isBanned()) {
            // Prevents infinite redirect loop
            if (basename(self::securevar($_SERVER['PHP_SELF'])) != 'banned.php') {
                header('location: banned.php');
            }
        }
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
        if (!Session::isAdmin()) {
            Util::redirect('/index.php');
        }
    }

    // supp check
    public static function suppCheck()
    {
        if (!Session::isSupp()) {
            Util::redirect('/index.php');
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
            return IMG_URL . $uid. ".png?" . Util::randomCode(5);
        } elseif (@getimagesize($path . ".jpg")) {
            return IMG_URL . $uid . ".jpg?". Util::randomCode(5);
        } elseif (@getimagesize($path . ".gif")) {
            return IMG_URL . $uid . ".gif?". Util::randomCode(5);
        } else {
            return false;
        }
    }
    public static function getavatardl($uid)
    {
        $path = IMG_DIR . $uid;
        if (@getimagesize($path . ".png")) {
            return IMG_URL . $uid. ".png" ;
        } elseif (@getimagesize($path . ".jpg")) {
            return IMG_URL . $uid . ".jpg";
        } elseif (@getimagesize($path . ".gif")) {
            return IMG_URL . $uid . ".gif";
        } else {
            return false;
        }
    }
}
