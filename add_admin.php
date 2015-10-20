<?php
session_start();

require_once 'classes/Administrator.php';
require_once 'classes/Util.php';

$util = new Util();
$util->validateAdmin();

$admin = new Administrator();
$error_message = "";
$info = "";

if (isset($_POST['Add'])) {
    $response = $admin->addAdmin($_POST['username'], $_POST['password'], $_POST['confirm_password'], $_POST['country']);
    if (!$response) {
        if (isset($_SESSION['error_message'])) {
            $error_message = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }
    } else {
        $info = "Admin created successfully!";
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Home - Recipes of the World</title>
        <?php include_once('meta.php'); ?>
    </head>
    <body>
    <?php include_once('header.php'); ?>

        <section id="section1">
            <?php include_once('_logout.php'); ?>
            <div  id="register-label">
                Create New Admin
            </div>
        </section>

        <section id="section4">
            <form method="post" action="">
                <table align="center" width="30%" cellpadding="2" cellspacing="5" border="0">
                    <?php if(!empty($error_message)) {
                    ?>
                    <tr>
                        <td colspan="2">
                            <div class="error"><?php echo $error_message; ?></div>
                            </td>
                    </tr>
                    <?php } ?>

                    <?php if(!empty($info)) {
                        ?>
                        <tr>
                            <td colspan="2">
                                <div class="info"><?php echo $info; ?></div>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td align="right">Username</td>
                        <td><input type="text" name="username" id="username" placeholder="Username" required></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">Password</td>
                        <td><input type="password" name="password" id="password" placeholder="Password" required></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm" required></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr align="center">
                        <td colspan="2">
                            <select name="country" id="country" required>
                                <option value="">Country</option>
                                <?php
                                $countries = require_once('_country.php');
                                foreach  ($countries as $key => $value) {
                                    ?>
                                    <option value="<?php echo $key;?>" <?php if (isset($country) && $key == $country) { echo "selected";} ?>>
                                        <?php echo $value; ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr align="center">
                        <td colspan="2"><button type="submit" name="Add">Register</button></td>
                    </tr>
                </table>
            </form>
        </section>
    </body>
</html>