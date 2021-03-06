<?php

class Jojo_Auth_Local {
    public static function authenticate() {
        global $_USERID, $_USERTIMEZONE, $_USERGROUPS;

        $username = Jojo::getFormData('username', '');
        $password = Jojo::getFormData('password', '');
        $remember = Jojo::getFormData('remember', false);

        $logindata = false;

        if ($username) {
            /* Allow logging in by email or just username? */
            if (Jojo::getOption('allow_email_login', 'no') == 'yes') {
                $userdata = Jojo::selectRow("SELECT * FROM {user} WHERE ((us_login = ?) OR (us_email = ?)) AND us_locked = 0", array($username, $username));
            } else {
                $userdata = Jojo::selectRow("SELECT * FROM {user} WHERE us_login = ? AND us_locked = 0", array($username));
            }

            if (!$userdata) { return false; }
            /* Try PHPass' Blowfish algo, then fall back to the older methods */
            if (self::checkPassword($password, $userdata["us_password"])) {
                /* Logged in */
                $logindata = $userdata;
                /* Old methods. Authenticate then upgrade. */
            } elseif (self::checkOldPassword($password, $userdata["us_password"], $userdata["us_salt"])) {
                /* Check if the password field has been upgraded */
                if (self::checkPasswordFieldLength()) {
                    /* Success, but let's upgrade the password */
                    $logindata = $userdata;
                    $newhash = self::hashPassword($password);
                    Jojo::updateQuery("UPDATE {user} SET us_password = ? WHERE userid = ?", array($newhash, $userdata["userid"]));
                }
                $logindata = $userdata;
            }

            if ($logindata) {
                /* Set up the values for the filter */
                $values = array($username, $username, $password, $password);
                $logindata = Jojo::applyFilter('auth_local_logindata', $logindata, $values);

                if (self::checkPasswordNeedsRehash($userdata["us_password"])) {
                    /* Update the hash count for this user's password to match the current global setting */
                    $newhash = self::hashPassword($password);
                    Jojo::updateQuery("UPDATE {user} SET us_password = ? WHERE userid = ?", array($newhash, $userdata["userid"]));
                }

                if ($logindata['us_failures'] > 0) {
                    /* Reset login failure count */
                    Jojo::updateQuery("UPDATE {user} SET us_failures = 0 WHERE userid = ?", $logindata['userid']);
                }

                if ($remember) {
                    /* Set remember password cookie */
                    $code = Jojo::randomstring(16);
                    setcookie("jojoR", base64_encode($logindata['userid'] . ':' . $code), time() + (60 * 60 * 24 * 365), '/' . _SITEFOLDER);
                    $values = array((int)($logindata['userid']), $code, time());
                    $res = Jojo::insertQuery("INSERT INTO {auth_token} SET userid = ?, token = ?, lastused = ?", $values);
                }

                /* After login hook */
                $_SESSION['loggingin'] = true;
  
              /* Store User Info */
                $_USERID = $logindata['userid'];
                $_USERTIMEZONE = $logindata['us_timezone'];
                $_SESSION['userid'] = $_USERID;

                /* Get User Group Membership */
                $_USERGROUPS = array('everyone');
                $groups = Jojo::selectQuery("SELECT * FROM {usergroup_membership} WHERE userid = ?", array($_USERID));

                /* if admin, set as admin ie to not show analytics when admin viewing site */
                foreach ($groups as $group) {
                    if($group['groupid'] != 'notloggedin') { // can't be both logged in, and in the usergroup 'notloggedin'
                       $_USERGROUPS[] = $group['groupid'];
                    }
                }
                return $logindata;
            }

            /* Login failed */

            /* Submitted a username but the password didn't match */
            $loginmessage = 'Your username or password is incorrect';
            if (Jojo::getOption('allow_email_login', 'no') == 'yes') {
                Jojo::updateQuery("UPDATE {user} SET us_lastfailure = NOW(), us_failures = us_failures + 1 WHERE us_login = ? OR us_email = ? LIMIT 1", array($username, $username));
            } else {
                Jojo::updateQuery("UPDATE {user} SET us_lastfailure = NOW(), us_failures = us_failures + 1 WHERE us_login = ? LIMIT 1", array($username));
            }

            /* Find out how many times user has failed - warn and lock if too many times */
            if (Jojo::getOption('allow_email_login', 'no') == 'yes') {
                $failures = Jojo::selectRow("SELECT us_failures FROM {user} WHERE us_login = ? OR us_email = ?", array($username, $username));
            } else {
                $failures = Jojo::selectRow("SELECT us_failures FROM {user} WHERE us_login = ?", array($username));
            }
            if (isset($failures['us_failures'])) {
                /* Warn after 5 failures */
                if ($failures['us_failures'] >= 5) {
                    $loginmessage = "You have entered your password incorrectly " . $failures['us_failures'] . " times.<br />You will be locked out after 10 incorrect attempts.";
                }

                /* Lock account after 10 failures */
                if ($failures['us_failures'] >= 10) {
                    $loginmessage = 'This account has been locked and must be unlocked by the administrator.<br/>Please contact <a href="mailto:' . Jojo::getOption('webmasteraddress') . '">' . Jojo::getOption('webmasteraddress') . "</a>";
                    if (Jojo::getOption('allow_email_login', 'no') == 'yes') {
                        Jojo::updateQuery("UPDATE {user} SET us_locked = 1 WHERE us_login = ? OR us_email = ? LIMIT 1", array($username, $username));
                    } else {
                        Jojo::updateQuery("UPDATE {user} SET us_locked = 1 WHERE us_login = ? LIMIT 1", array($username));
                    }
                }
            }

            /* Delete Cookie */
            if (isset($_COOKIE['remember'])) {
                setcookie('jojoR', '', time() - 3600, _SITEFOLDER);
            }

            /* User is not logged in */
            $_USERID = false;
            $_USERGROUPS[] = 'notloggedin';
            
            /* Error message to return to user */
            return $loginmessage;
        }
    }

    /* Hash a password using PHPass' blowfish algo. Salts are auto handled in the hash. */
    public static function hashPassword($password, $install=false) {
        require_once(_BASEDIR."/plugins/jojo_core/external/phpass/PasswordHash.php");
        $hashCount = $install ? 10 : Jojo::getOption("password_cost", 10);
        $phpass = new PasswordHash($hashCount, false); // The 'false' triggers Blowfish if available. */
        return $phpass->HashPassword($password);
    }

    /* Check a password */
    public static function checkPassword($password, $hash, $salt=false) {
        $hashCount = Jojo::getOption("password_cost", 10);

        // Try PHPass' Blowfish algo first
        require_once(_BASEDIR."/plugins/jojo_core/external/phpass/PasswordHash.php");
        $phpass = new PasswordHash($hashCount, false);
        if ($hash && $phpass->CheckPassword($password, $hash)) {
            return true;
        }
        // Try the old methods
        if ($salt && self::checkOldPassword($password, $hash, $salt)) {
            return true;
        }
        return false;
    }

    /* Check old SHA1 and MD5 passwords, but don't upgrade them. */
    public static function checkOldPassword($password, $hash, $salt) {
        if (SHA1($password.$salt) == $hash) { return true; }
        if (MD5($password.$salt) == $hash) { return true; }
        return false;
    }

    /* Get password field length */
    public static function checkPasswordFieldLength() {
        $pwField = Jojo::selectRow("SHOW COLUMNS FROM {user} WHERE Field = 'us_password'");
        if (!$pwField) { return false; }
        $pwField = strtolower($pwField["Type"]);
        $pwField = (int)str_replace(array("varchar(", ")"), "", $pwField);
        return ($pwField == 255);
    }

    public static function checkBuiltinPasswordFunctions() {
        return (function_exists("password_hash") && function_exists("password_verify") && function_exists("password_needs_rehash"));
    }

    /* Check if a password should be rehashed to match newer security settings */
    public static function checkPasswordNeedsRehash($password) {
        $segments = explode("$", $password);
        return isset($segments[2]);
    }
}
