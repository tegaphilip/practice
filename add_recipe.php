<?php
session_start();

require_once 'classes/Recipe.php';
require_once 'classes/Category.php';
require_once 'classes/Util.php';

$util = new Util();
$util->validateUser();

$category = new Category();
$categories = $category->getAllCategories();
$error_message = "";
$info = "";

if (isset($_POST['Add'])) {

    $recipe = new Recipe();

    $country = $_POST['country'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $tags = $_POST['tags'];

    $response = $recipe->addRecipe($name, $country, $tags, $description);

    if (!$response) {
        if (isset($_SESSION['error_message'])) {
            $error_message = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }
    } else {
        $info = "Recipe created successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Add Recipe - Recipes of the World</title>
        <?php include_once('meta.php'); ?>
    </head>
    <body>
    <?php include_once('header.php'); ?>

        <section id="section1">
            <?php include_once('_logout.php'); ?>
            <div class="label" id="add-recipe-label">Add new recipe</div>
        </section>

        <section id="section4">
            <?php include_once('_tag_pop_up.php'); ?>



            <form method="post" action="" id="add-recipe-form">
                <table width="30%" cellpadding="2" cellspacing="5" border="0">
                    <?php if(!empty($error_message)) {
                        ?>
                        <tr>
                            <td colspan="2">
                                <div class="error"><?php echo $error_message; ?></div>
                            </td>
                        </tr>
                        <?php
                    } ?>
                    <?php if(!empty($info)) {
                        ?>
                        <tr>
                            <td colspan="2">
                                <div class="info"><?php echo $info; ?></div>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
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
                        <td colspan="2">
                            <input type="text" name="name" id="name" placeholder="Name" value="<?php if( isset($name)) { echo $name;} ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <textarea required name="description" id="description" placeholder="Description" rows="15" cols="50"><?php if( isset($description)) { echo $description;} ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">Select Tags (hold the control key to select multiple tags)</td>
                    </tr>
                    <tr>
                        <td>
                            <select multiple name="tags[]" id="tags">
                                <?php
                                    foreach ($categories as $cat) {
                                        ?>
                                <option value="<?php echo $cat['id']; ?>" <?php if (isset($tags) && in_array($cat['id'], $tags)) { echo "selected";} ?>>
                                    <?php echo $cat['description']; ?>
                                </option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </td>
                        <td><a href="add_category.php">Add New</a></td>
                    </tr>
                    <tr>
                        <td><button type="reset">Cancel</button></td>
                        <td align="right"><button type="submit" name="Add">Add</button></td>
                    </tr>
                </table>
            </form>
        </section>
    </body>
</html>

<script>

</script>