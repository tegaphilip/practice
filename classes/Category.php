<?php

class Category
{
    public function addCategory($tagName)
    {
        $dbConnection = new DBConnection();
        $mysqli = $dbConnection->getDBConnection();

        if (is_null($mysqli)) {
            $_SESSION['error_message'] = "There was a problem connecting with the database";
            return false;
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "You are not logged in";
            header("Location:index.php");
        }

        //check if recipe with that name has been created before
        $tagName = $mysqli->real_escape_string($tagName);
        $sql = "SELECT * FROM tags WHERE description = '$tagName'";
        $result = $dbConnection->getResultSet($sql);

        if (!empty($result)) {
            $_SESSION['error_message'] = "A tag with that name has already been created";
            return false;
        }

        //Insert the tag
        $id = $_SESSION['user_id'];

        $sql = "INSERT INTO tags (description,  userID) VALUES ('$tagName','$id')";
        $result = $dbConnection->executeUpdate($sql);
        if (empty($result)) {
            $_SESSION['error_message'] = "Tag creation failed";
            return false;
        }

        return true;
    }


    public function editCategory($tagId, $tagName)
    {
        $dbConnection = new DBConnection();
        $mysqli = $dbConnection->getDBConnection();

        if (is_null($mysqli)) {
            $_SESSION['error_message'] = "There was a problem connecting with the database";
            return false;
        }

        //check if recipe with that name has been created before
        $tagName = $mysqli->real_escape_string($tagName);
        $sql = "SELECT * FROM tags WHERE description = '$tagName' AND id <> $tagId";
        $result = $dbConnection->getSingleResultSet($sql);

        if (!empty($result)) {
            $_SESSION['error_message'] = "A category with that name has already been created";
            return false;
        }

        //Update the recipe with the appropriate recipe tags
        $sql = "UPDATE tags
                    SET description = '$tagName',
                WHERE id = $tagId";
        $result = $dbConnection->executeUpdate($sql);
        if (empty($result)) {
            $_SESSION['error_message'] = "Category update failed";
            return false;
        }

        return true;
    }

    /**
     * @return array|bool
     */
    public function getAllCategories()
    {
        $db = new DBConnection();

        return $db->getMultipleResultSet("SELECT * FROM tags ORDER BY description");
    }
}