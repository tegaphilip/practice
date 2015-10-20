<?php
session_start();
/**
 * Created by PhpStorm.
 * User: tegaoghenekohwo
 * Date: 20/10/15
 * Time: 22:26
 */
class Util
{

    /**
     * This is just a random way to make sure that people do not play with the url parameters
     * @param $id
     * @return mixed
     */
    public function encryptPK($id)
    {
        //multiply number by 138628;
        $id = $id * 138628;
        //add 789 to it
        $id = $id + 789;
        //multiply again by 93
        return $id * 93;
    }

    /**
     * This is the decoding algorithm
     * @param $number
     * @return float
     */
    public function decryptPK($number)
    {
        //divide number by 93
        $number = $number / 93;
        //subtract 789
        $number -= 789;

        //divide lastly by 138628
        return $number/138628;
    }

    /**
     *
     */
    public function validateUser()
    {
        if (!isset($_SESSION['is_logged_in'])) {
            $_SESSION['error_message'] = "You are not logged in!";
            header("Location:index.php");
        }
    }

    /**
     *
     */
    public function validateAdmin()
    {
        $this->validateUser();

        if (!isset($_SESSION['admin']) || $_SESSION['admin'] == 0) {
            $_SESSION['error_message'] = "You are not authorised to view this page";
            header("Location:index.php");
        }
    }


    /**
     * Encrypt password
     * @param $password
     * @return bool|string
     */
    public function encryptPassword($password)
    {
        $options = [
            'cost' => 11,
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
        ];
        return  password_hash($password, PASSWORD_BCRYPT, $options);
    }
}