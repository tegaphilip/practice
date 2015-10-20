<?php
session_start();
$error_message = "";
if (!isset($_SESSION['is_logged_in'])) {
    $_SESSION['error_message'] = "You are not logged in!";
    header("Location:index.php");
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
            <div id="logout-button">
                <a href="logout.php">
                    <button type="button" name="Logout" id="Logout">Logout</button>
                </a>
            </div>
            <div class="label" id="add-recipe-label">Add new recipe</div>
        </section>

        <section id="section4">
            <?php include_once('_tag_pop_up.php'); ?>



            <form method="post" action="add_recipe_action.php" id="add-recipe-form">
                <table width="30%" cellpadding="2" cellspacing="5" border="0">
                    <tr>
                        <td colspan="2">
                            <select name="country" id="country">
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
                        <td colspan="2">
                            <input type="text" name="name" id="name" placeholder="Name">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <textarea name="description" id="description" placeholder="Description" rows="15" cols="50"></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td>Select Tags</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <select multiple name="tags" id="tags">
                                <option>Chinese</option>
                                <option>Nierian</option>
                                <option>Cousine</option>
                                <option>CousCoussssssssssss</option>
                            </select>
                        </td>
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