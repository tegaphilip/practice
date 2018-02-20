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


    public static function uploadFile($dollarUnderScoreFile, $type, $directory)
    {
        $maxFileSize = 100; //40mb
        $response = array();
        switch ($type) {
            case "image":
                $allowed_extensions = array("jpg", "jpeg", "png", "gif");
                $extension = strtolower(Util::getFileExtension($dollarUnderScoreFile['name']));
                if (!in_array($extension, $allowed_extensions)) {
                    $response['status'] = false;
                    $response['error'] = "Invalid image file";
                    return $response;
                }
                if ($dollarUnderScoreFile['size'] > $maxFileSize * 1024 * 1024) {
                    $response['status'] = false;
                    $response['error'] = "Image size must not be greater than {$maxFileSize} MB";
                    return $response;
                }
                $uploaded_name = Util::upload($dollarUnderScoreFile, $directory);
                if ($uploaded_name == -1) {
                    $response['status'] = false;
                    $response['error'] = "Failed uploading image";
                    return $response;
                }
                $response['status'] = true;
                $response['uploaded_name'] = $uploaded_name;
                return $response;
                break;
            default:
                break;
        }
        return false;
    }


    public static function getFileExtension($fname)
    {
        return strtolower(substr($fname, strrpos($fname, ".") + 1));
    }

    public static function upload($path, $upload_dir)
    {
        $random = rand() . substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 3) .
            date("YmdHis") .
            str_replace(" ", "_", $path['name']);
        if (move_uploaded_file($path['tmp_name'], $upload_dir . "/" . $random)) {
            return $random;
        } else {
            return -1;
        }
    }
}