<?php
session_start();
require_once 'classes/Recipe.php';
require_once 'classes/Util.php';

$error_message = "";
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$recipe = new Recipe();
$util = new Util();

$recipes = array();
if (isset($_GET['name']) || isset($_GET['description']) || isset($_GET['tags']) || isset($_GET['country']) || isset($_GET['only_mine'])) {
    $name = isset($_GET['name']) ? $_GET['name'] : "";
    $description = isset($_GET['description']) ? $_GET['description'] : "";
    $tags = isset($_GET['tags']) ? $_GET['tags'] : "";
    $country = isset($_GET['country']) ? $_GET['country'] : "";
    $onlyMine = isset($_GET['only_mine']) && $_GET['only_mine'] == "on" ? 1 : 0;
    $recipes = $recipe->getFilteredRecipes($name, $description, $tags, $country, $onlyMine);
} else {
    $recipes = $recipe->getAllRecipes();
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
            <?php if(!empty($error_message)) {
                ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php
            } ?>
            <?php if (!isset($_SESSION['is_logged_in'])) {
                ?>
                <form method="post" id="login-form" action="login.php">
                    <input type="text" id="username" name="username" placeholder="Username" required/>
                    <input type="password" id="password" name="password" required/>
                    <button type="submit" name="Login" id="Login">Login</button>
                </form>
                <a href="register.php" id="register-link">
                    <button>Register</button>
                </a>
                <?php
            } else {
                echo "Hello " . htmlentities($_SESSION['username']) . "!      ";
                ?>
                <a href="logout.php">
                    <button type="button" name="Logout" id="Logout">Logout</button>
                </a>
                <?php
            }?>

            The Recipes!
            <?php if (isset($_SESSION['is_logged_in'])) {
                if ($_SESSION['admin'] == 0) {
                    ?>
                    <a href="add_recipe.php" id="new-recipe-link">
                        <button>Add New Recipe</button>
                    </a>
                    <?php
                } else {
                    ?>
                    <a href="settings.php" id="new-recipe-link">
                        <button>Admin Settings</button>
                    </a>
                    <?php
                }
            } ?>

        </section>

        <section id="section2">
            <form method="get" id="filter-form">
                <span>Filter</span>
                <input type="text" id="name" name="name" placeholder="Name" value="<?php if(isset($_GET['name'])) {echo $_GET['name'];} ?>"/>
                <input type="text" id="descriptions" name="description" placeholder="Description" value="<?php if(isset($_GET['description'])) {echo $_GET['description'];} ?>"/>
                <input type="text" id="tags" name="tags" placeholder="Tag(s)" value="<?php if(isset($_GET['tags'])) {echo $_GET['tags'];} ?>"/>

                <select name="country" id="country">
                    <option value="">Country</option>
                    <?php
                    $countries = require_once('_country.php');
                        foreach  ($countries as $key => $value) {
                        ?>
                        <option value="<?php echo $key;?>" <?php if (isset($_GET['country']) && $key == $_GET['country']) { echo "selected";} ?>>
                            <?php echo $value; ?>
                        </option>
                        <?php
                        }
                    ?>
                </select>
                <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['admin'] == 0) {
                    ?>
                    <input type="checkbox" name="only_mine" id="only-mine" <?php if (isset($_GET['only_mine']) && $_GET['only_mine'] == "on") {echo "checked";} ?>/>
                    <label for="only-mine" id="label-for-only-recipes">Only my recipes</label>
                    <?php
                } ?>

                <button name="filter" id="filter" type="submit">Filter Now</button>
            </form>
        </section>
        <section id="section3">
            <div id="instruction">(Click a recipe to view more details)</div>
        </section>
        <section id="section4">
            <table width="100%" cellpadding="5" cellspacing="5" id="recipe-list">
                <tr align="left" class="alternate">
                    <th>Name</th>
                    <th>Description</th>
                    <th>Country</th>
                    <th>Username</th>
                </tr>

                <?php
                $i = 0;
                foreach ($recipes as $line) {
                    ?>
                        <tr class="<?php echo $i % 2 == 0 ? '' : 'alternate'; ?>">
                            <td>
                                <a class="no-decorate" href="view_recipe.php?recipe_id=<?php echo $util->encryptPK($line['recipe_id']); ?>">
                                    <?php echo $line['recipe_name'];?>
                                </a>
                            </td>
                            <td>
                                <a class="no-decorate" href="view_recipe.php?recipe_id=<?php echo $util->encryptPK($line['recipe_id']); ?>">
                                    <?php echo strlen($line['description']) > 80 ? "<span class='ellipsis'>" . substr($line['description'],0,80)."...</span>" : $line['description']; ?>
                                </a>
                            </td>
                            <td>
                                <a class="no-decorate" href="view_recipe.php?recipe_id=<?php echo $util->encryptPK($line['recipe_id']); ?>">
                                    <?php echo $line['country'];?>
                                </a>
                            </td>
                            <td>
                                <a class="no-decorate" href="view_recipe.php?recipe_id=<?php echo $util->encryptPK($line['recipe_id']); ?>">
                                    <?php echo $line['username'];?>
                                </a>
                            </td>
                        </tr>

                    <?php
                    $i++;
                }
                ?>

                <tr>
                    <td colspan="2">
                        <div id="fb-root"></div><script>(function(d, s, id) {  var js, fjs = d.getElementsByTagName(s)[0];  if (d.getElementById(id)) return;  js = d.createElement(s); js.id = id;  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";  fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script><div class="fb-post" data-href="https://www.facebook.com/iamsalhazain/videos/804134492947405/" data-width="500"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/iamsalhazain/videos/804134492947405/"><p>This Street Musician Was Tipped By A Girl. What Happened Next Blew The Whole City Away.www.salhazain.com</p>Posted by <a href="https://www.facebook.com/iamsalhazain/">Salha Zain</a> on&nbsp;<a href="https://www.facebook.com/iamsalhazain/videos/804134492947405/">Sunday, March 30, 2014</a></blockquote></div></div>
                    </td>
                </tr>
            </table>
        </section>
    </body>
</html>