<?php
session_start();
require_once 'classes/Util.php';

$util = new Util();
$util->validateAdmin();
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
            <div class="label" id="add-recipe-label">Admin Panel</div>
        </section>

        <section id="section4">
            <?php include_once('_tag_pop_up.php'); ?>


            <div id="add-recipe-form">
                <table width="30%" cellpadding="20" cellspacing="20" border="0">

                    <tr>
                        <td>
                            <a href="add_admin.php">
                                <button class="very-large">Add New Admin</button>
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a href="categories.php">
                                <button class="very-large">Manage Categories</button>
                            </a>
                        </td>

                    </tr>

                </table>
            </div>
        </section>
    </body>
</html>

<script>

</script>