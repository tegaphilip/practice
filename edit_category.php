<?php
session_start();

require_once 'classes/Util.php';
require_once 'classes/Category.php';

$util = new Util();
$util->validateAdmin();

$category = new Category();
$error_message = "";
$info = "";

if (isset($_POST['Edit'])) {
    $response = $category->editCategory($_POST['id'],$_POST['name']);
    if (!$response) {
        if (isset($_SESSION['error_message'])) {
            $error_message = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }
    } else {
        $info = "Category edited successfully!";
    }
}

$categoryDetails = array();
if (isset($_GET['category_id'])) {
    $categoryId = $util->decryptPK($_GET['category_id']);
    $categoryDetails = $category->getCategory($categoryId);

    if (empty($categoryDetails)) {
        $_SESSION['error_message'] = "The page you are looking for does not exist!";
        header("Location:index.php");
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
                Edit Category
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
                        <td><input type="text" name="name" id="name" placeholder="Name" required value="<?php echo $categoryDetails['description']; ?>"></td>
                    </tr>

                    <tr align="center">
                        <td colspan="2"><button type="submit" name="Edit">Update</button></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="hidden" name="id" value="<?php if (isset($categoryId)) { echo $categoryId; } ?>"></td>
                    </tr>
                </table>
            </form>
        </section>
    </body>
</html>