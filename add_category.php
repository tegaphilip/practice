<?php
session_start();

require_once 'classes/Util.php';
require_once 'classes/Category.php';

$util = new Util();
$util->validateUser();

$category = new Category();
$error_message = "";
$info = "";

if (isset($_POST['Add'])) {
    $response = $category->addCategory($_POST['name']);
    if (!$response) {
        if (isset($_SESSION['error_message'])) {
            $error_message = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }
    } else {
        $info = "Category created successfully!";
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
                Create New Category
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
                        <td align="right">Name</td>
                        <td><input type="text" name="name" id="name" placeholder="Name" required></td>
                    </tr>

                    <tr align="center">
                        <td colspan="2"><button type="submit" name="Add">Save</button></td>
                    </tr>
                </table>
            </form>
        </section>
    </body>
</html>