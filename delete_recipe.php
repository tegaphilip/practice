<?php
session_start();

require_once 'classes/Recipe.php';
require_once 'classes/Util.php';

$util = new Util();
$util->validateUser();

$recipe = new Recipe();

if (isset($_GET['recipe_id'])) {
    $recipeId = $util->decryptPK($_GET['recipe_id']);
    $recipe->deleteRecipe($recipeId);
}

header("Location:index.php");