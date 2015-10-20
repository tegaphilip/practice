<?php
session_start();

require_once 'classes/Util.php';
require_once 'classes/Category.php';

$util = new Util();
$util->validateAdmin();

$category = new Category();

if (isset($_GET['category_id'])) {
    $categoryId = $util->decryptPK($_GET['category_id']);
    $category->deleteCategory($categoryId);
}

header("Location:categories.php");