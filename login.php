<?php
session_start();
require_once('param.inc.php');
//require_once('classes/Utility.php');
if (isset($_POST['Login'])) {
    //this is just to check what was submitted that it was not empty, db connection not needed yet
    //escaping of string will be done before sending request to DB
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error_message'] = "Sorry, you must enter a username and a password. Please try again!";
    } else {
        //connect to mysql database
        $mysqli = new mysqli($db_host,$db_login,$db_password,$db_name);
        //$username = $mysqli->real_escape_string($username);
        //$password = $mysqli->real_escape_string($password);

        if ($mysqli->connect_errno) {
            $_SESSION['error_message'] = "Sorry, there is a problem with our server at the moment. Please try again shortly";
        } else {
            $query = "SELECT * FROM users WHERE username = '$username'";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error_message'] = "Sorry, there is a problem with our server at the moment. Please try again shortly";
            } else if ($result->num_rows == 0) {
                $_SESSION['error_message'] = "Sorry, your credentials are incorrect. Please try again";
            } else {
                //username is valid, verify password
                //login is valid, set session data
                $userDetails = $result->fetch_assoc();
                if (password_verify($password, $userDetails['password'])) {
                    $_SESSION['is_logged_in'] = true;
                    $_SESSION['username'] = $userDetails['username'];
                    $_SESSION['user_id'] = $userDetails['id'];
                    $_SESSION['admin'] = $userDetails['is_admin'];
                } else {
                    $_SESSION['error_message'] = "Sorry, your credentials are incorrect. Please try again";
                }
            }
        }
    }

    header("Location:index.php");
}