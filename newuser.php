<?php
session_start();
require_once('param.inc.php');
if (isset($_POST['Register'])) {

    //This is where Lilian begins
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $country = $_POST['country'];

    if (empty($username) || empty($password) || empty($confirm_password) || empty($country)) {
        $_SESSION['error_message'] = "Sorry, you must enter a username, password, and country. Please try again!";
    } else if ($password != $confirm_password) {
        $_SESSION['error_message'] = "Sorry, you must enter the same password. Please try again!";
    } else {
        //connect to mysql database
        $mysqli = new mysqli($db_host, $db_login, $db_password, $db_name);
        if ($mysqli->connect_errno) {
            $_SESSION['error_message'] = "Sorry, there is a problem with our server at the moment. Please try again shortly";
        } else {
            $queryString = "SELECT * FROM users WHERE username = '$username'";
            $result = $mysqli->query($queryString);

            if (!$result) {
                $_SESSION['error_message'] = "Sorry, there is a problem with our server at the moment. Please try again shortly";
            } else if ($result->num_rows > 0) {
                $_SESSION['error_message'] = "Sorry, this username is already in use. Please change!";
            } else {
                $options = [
                    'cost' => 11,
                    'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
                ];
                $passwordEncrypted = password_hash($password, PASSWORD_BCRYPT, $options);

                $queryString = "INSERT INTO users (username,password,is_admin,country)
                                           VALUES ('$username', '$passwordEncrypted', 0, '$country')";

                $result = $mysqli->query($queryString);

                if (!$result) {
                    $_SESSION['error_message'] = "Sorry, there is a problem with our server at the moment. Please try again shortly";
                } else if ($mysqli->affected_rows == 0) {
                    $_SESSION['error_message'] = "Sorry, but your records were not saved. Our database hates you! Please try again shortly";
                } else if ($mysqli->affected_rows > 0) {
                    $_SESSION['info'] = "Congratulations buddy!!!! You have successfully registered. Bravo!!";
                }
            }
        }
    }
    header("Location:register.php");
}