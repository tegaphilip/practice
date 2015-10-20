<?php
session_start();
$error_message = "";
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
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
                ?>
                <a href="add_recipe.php" id="new-recipe-link">
                    <button>Add New Recipe</button>
                </a>
                <?php
            } ?>

        </section>

        <section id="section2">
            <form method="get" id="filter-form">
                <span>Filter</span>
                <input type="text" id="name" name="name" placeholder="Name"/><input type="text" id="descriptions"
                                                                                     name="description"
                                                                                     placeholder="Description"/>
                <input type="text" id="tags" name="tags" placeholder="Tag(s)"/>
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
                <?php if (isset($_SESSION['is_logged_in'])) {
                    ?>
                    <input type="checkbox" name="only_mine" id="only-mine"/>
                    <label for="only-mine" id="label-for-only-recipes">Only my recipes</label>
                    <?php
                } ?>

                <button name="filter" id="filter" type="submit">Filter Now</button>
            </form>
        </section>
        <section id="section3">
            <div id="instruction">(Click a recipe to view it or modify ones you have permissions to)</div>
        </section>
        <section id="section4">
            <table width="100%" cellpadding="2" cellspacing="2" id="recipe-list">
                <tr align="left" class="alternate">
                    <th>Name</th>
                    <th>Description</th>
                    <th>Country</th>
                    <th>Username</th>
                </tr>

                <tr>
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr class="alternate">
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr>
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr class="alternate">
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr>
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr class="alternate">
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr>
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr class="alternate">
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr>
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr class="alternate">
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr>
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr class="alternate">
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr>
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr class="alternate">
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr>
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr class="alternate">
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr>
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>

                <tr class="alternate">
                    <td>Name 1</td>
                    <td>Description 1</td>
                    <td>Country 1</td>
                    <td>Username 1</td>
                </tr>
            </table>
        </section>
    </body>
</html>