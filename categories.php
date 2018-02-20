<?php
session_start();
require_once 'classes/Util.php';
require_once 'classes/Category.php';

$util = new Util();
$util->validateAdmin();

$error_message = "";
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$category = new Category();

$categories = $category->getAllCategories();
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
            <?php
            include_once '_logout.php';
            if(!empty($error_message)) {
                ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php
            }
            ?>


                <a href="settings.php" id="new-recipe-link">
                    <button>Admin Settings</button>
                </a>

        </section>
        <section id="section3">
            <div id="instruction">(Categories Management) <a href="add_category.php">Add New</a></div>
        </section>

        <section id="section4">
            <table width="100%" cellpadding="5" cellspacing="5" id="recipe-list">
                <tr align="left" class="alternate">
                    <th>Name</th>
                    <th>Added By</th>
                    <th colspan="2" align="center">Actions</th>
                </tr>

                <?php
                $i = 0;
                foreach ($categories as $line) {
                    ?>
                        <tr class="<?php echo $i % 2 == 0 ? '' : 'alternate'; ?>">
                            <td>
                                <?php echo $line['description']; ?>
                            </td>
                            <td>
                                <?php echo $line['username']; ?>
                            </td>
                            <td>
                                <a href="edit_category.php?category_id=<?php echo $util->encryptPK($line['id']); ?>">Edit</a>
                            </td>
                            <td>
                                <a href="#" onclick="return validateDelete(<?php echo $util->encryptPK($line['id']);?>)">Delete</a>
                            </td>
                        </tr>

                    <?php
                    $i++;
                }
                ?>
            </table>
        </section>
    </body>
</html>

<script>
    function validateDelete(categoryId) {
        if (!confirm('Are you sure you want to delete this item permanently?')) {
            return false;
        }

        window.location = 'delete_category.php?category_id=' + categoryId;
    }
</script>