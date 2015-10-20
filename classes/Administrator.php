<?php

require_once 'DBConnection.php';
require_once 'Util.php';

class Administrator
{

    /**
     * Create new administrator
     * @param $username
     * @param $password
     * @param $confirm_password
     * @param $country
     * @return bool
     */
    public function addAdmin($username, $password, $confirm_password, $country)
    {
        $db = new DBConnection();
        $util = new Util();

        if (empty($username) || empty($password) || empty($confirm_password) || empty($country)) {
            $_SESSION['error_message'] = "Sorry, you must enter a username, password, and country. Please try again!";
        } else if ($password != $confirm_password) {
            $_SESSION['error_message'] = "Sorry, you must enter the same password. Please try again!";
        } else {

            $queryString = "SELECT * FROM users WHERE username = '$username'";
            $result = $db->getMultipleResultSet($queryString);

            if (!empty($result)) {
                $_SESSION['error_message'] = "Sorry, this username is already in use. Please change!";
                return false;
            } else {
                $passwordEncrypted = $util->encryptPassword($password);

                $queryString = "INSERT INTO users (username,password,is_admin,country)
                                           VALUES ('$username', '$passwordEncrypted', 1, '$country')";

                $result = $db->executeInsert($queryString);

                if (!$result) {
                    $_SESSION['error_message'] = "Sorry, there is a problem with our server at the moment. Please try again shortly";
                    return false;
                } else  {
                    $_SESSION['info'] = "Congratulations buddy!!!! You have successfully registered. Bravo!!";
                    return true;
                }
            }

        }

        return true;
    }
}