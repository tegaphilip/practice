<?php
session_start();

require_once 'classes/Recipe.php';
require_once 'classes/Category.php';
require_once 'classes/Util.php';

$util = new Util();

$category = new Category();
$categories = $category->getAllCategories();

$recipe = new Recipe();

$recipeDetails = array();;
$tags = array();
$recipeId = "";

if (isset($_GET['recipe_id'])) {
    $recipeId = $util->decryptPK($_GET['recipe_id']);
    $recipeDetails = $recipe->getRecipeOnly($recipeId);

    if (!empty($recipeDetails)) {
        $recipeAndTags = $recipe->getRecipeAndTags($recipeId);
        $tags = array_column($recipeAndTags, 'tag_name');
    } else {
        $_SESSION['error_message'] = "The page you are looking for does not exist!";
        header("Location:index.php");
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>View Recipe - Recipes of the World</title>
        <?php include_once('meta.php'); ?>
    </head>
    <body>
    <?php include_once('header.php'); ?>

        <section id="section1">
            <?php include_once('_logout.php'); ?>
        </section>

        <section id="section4">
            <?php include_once('_tag_pop_up.php'); ?>

            <form method="post" action="" id="add-recipe-form">
                <table width="30%" cellpadding="5" cellspacing="5" border="0">
                    <tr>
                        <td></td>
                        <td>
                            <?php
                                $image = "default.png";
                                if (isset($recipeDetails['photo'])) {
                                    $image = $recipeDetails['photo'];
                                }
                            ?>
                            <img src="image_uploads/<?php echo $image; ?>" width="200" height="200">
                        </td>
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td><?php echo $recipeDetails['country']; ?></td>
                    </tr>

                    <tr>
                        <td>Name</td>
                        <td>
                            <?php echo $recipeDetails['name']; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Description (How to?)
                        </td>
                        <td>
                            <?php echo $recipeDetails['description']; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Tag (s)</td>
                        <td>
                            <?php echo implode(", ", $tags); ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <?php if ($_SESSION['admin'] == 1 || $_SESSION['user_id'] == $recipeDetails['userID']) {
                                ?>
                                <a onclick="return validateDelete(<?php echo $util->encryptPK($recipeId);?>)">
                                    <button type="button">Delete Recipe</button>
                                </a>
                        </td>
                            <?php
                            } ?>

                        <?php if  ($_SESSION['user_id'] == $recipeDetails['userID']) {
                            ?>
                            <td>
                                <a href="edit_recipe.php?recipe_id=<?php echo $util->encryptPK($recipeId);?>">
                                    <button type="button">Edit Recipe</button>
                                </a>
                            </td>

                            <?php
                        }?>

                    </tr>
                </table>
            </form>
        </section>
    </body>
</html>

<script>
    function validateDelete(recipeId) {
        if (!confirm('Are you sure you want to delete this item permanently?')) {
            return false;
        }

        window.location = 'delete_recipe.php?recipe_id=' + recipeId;
    }
</script>