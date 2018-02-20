<?php
session_start();

require_once 'classes/Recipe.php';
require_once 'classes/Category.php';
require_once 'classes/Util.php';

$util = new Util();
$util->validateUser();

$category = new Category();
$categories = $category->getAllCategories();

$recipe = new Recipe();

$error_message = "";
$info = "";

if (isset($_POST['Edit'])) {
    $country = $_POST['country'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $tags = $_POST['tags'];
    $recipeId = $_POST['recipe_id'];

    $response = $recipe->editRecipe($recipeId, $name, $country, $tags, $description);

    if (!$response) {
        if (isset($_SESSION['error_message'])) {
            $error_message = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }
    } else {
        $info = "Recipe updated successfully!";
    }
}

$recipeDetails = [];

if (isset($_GET['recipe_id'])) {
    $recipeId = $util->decryptPK($_GET['recipe_id']);
    $recipeDetails = $recipe->getRecipeOnly($recipeId);

    if (!empty($recipeDetails)) {
        $recipeAndTags = $recipe->getRecipeAndTags($recipeId);
        $tags = array_column($recipeAndTags, 'tag_id');
    } else {
        $_SESSION['error_message'] = "The page you are looking for does not exist!";
        header("Location:index.php");
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Edit Recipe - Recipes of the World</title>
        <?php include_once('meta.php'); ?>
    </head>
    <body>
    <?php include_once('header.php'); ?>

        <section id="section1">
            <?php include_once('_logout.php'); ?>
            <div class="label" id="add-recipe-label">Edit Recipe <?php echo $recipeDetails['name']; ?></div>
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
                            <select name="country" id="country" required <?php if ($_SESSION['admin'] == 1) { echo "readonly";} ?>>
                                <option value="">Country</option>
                                <?php
                                    $countries = require_once('_country.php');
                                    foreach  ($countries as $key => $value) {
                                        ?>
                                    <option value="<?php echo $key;?>" <?php if (isset($recipeDetails['country']) && $key == $recipeDetails['country']) { echo "selected";} ?>>
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
                            <input type="text" name="name" id="name" placeholder="Name" value="<?php if( isset($recipeDetails['name'])) { echo $recipeDetails['name'];} ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <textarea required name="description" id="description" placeholder="Description" rows="15" cols="50"><?php if( isset($recipeDetails['description'])) { echo $recipeDetails['description'];} ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">Select Tags (hold the control key to select multiple tags)</td>
                    </tr>
                    <tr>
                        <td colspan="2">
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
                    </tr>
                    <tr>
                        <td>
                            <?php if ($_SESSION['admin'] == 1) {
                                ?>

                            <?php
                            } else {
                                ?>
                            <button type="reset">Cancel</button></td>
                            <?php
                            }?>

                        <td align="right"><button type="submit" name="Edit">Update</button></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="hidden" name="recipe_id" value="<?php if (isset($recipeId)) { echo $recipeId; } ?>"></td>
                    </tr>
                </table>
            </form>
        </section>
    </body>
</html>