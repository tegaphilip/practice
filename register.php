<?php
session_start();
$error_message = "";
$info = "";
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['info'])) {
    $info = $_SESSION['info'];
    unset($_SESSION['info']);
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
            <div  id="register-label">
                Register for Free!
            </div>
        </section>

        <section id="section4">
            <form method="post" action="newuser.php">
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
                                <option value="China">China</option>
                                <option value="France">France</option>
                                <option value="Germany">Germany</option>
                                <option value="Ghana">Ghana</option>
                                <option value="India">India</option>
                                <option value="Nepal">Nepal</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="USA">USA</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr align="center">
                        <td colspan="2"><button type="submit" name="Register">Register</button></td>
                    </tr>
                </table>
            </form>
        </section>
    </body>
</html>