<?php

require_once 'DBConnection.php';

class Recipe
{
    /**
     * @param $name
     * @param $country
     * @param array $tags
     * @param $description
     * @return bool
     */
    public function addRecipe($name, $country, $tags = [], $description)
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

        if (empty($name) || empty($country) || empty($description)) {
            $_SESSION['error_message'] = "Please fill in all required fields";
            return false;
        }

        //check if recipe with that name has been created before
        $name = $mysqli->real_escape_string($name);
        $sql = "SELECT * FROM recipes WHERE name = '$name'";
        $result = $dbConnection->getMultipleResultSet($sql);

        if (!empty($result)) {
            $_SESSION['error_message'] = "A recipe with that name has already been created";
            return false;
        }

        //Insert the recipe with the appropriate recipe tags
        $country = $mysqli->real_escape_string($country);
        $description = $mysqli->real_escape_string($description);
        $id = $_SESSION['user_id'];

        $sql = "INSERT INTO recipes (name, description, country, userID) VALUES ('$name','$description','$country','$id')";
        $insertId = $dbConnection->executeInsert($sql);
        if (empty($insertId)) {
            $_SESSION['error_message'] = "Recipe creation failed";
            return false;
        }

        //insert into recipe tags table based on the tags of the recipes
        foreach ($tags as $tag_id) {
            $sql = "INSERT INTO recipetags (recipeID, tagID) VALUES ($insertId, $tag_id)";
            $dbConnection->executeUpdate($sql);
        }

        return true;
    }

    /**
     * @param $recipeId
     * @param $name
     * @param $country
     * @param array $tags
     * @param $description
     * @return bool
     */
    public function editRecipe($recipeId, $name, $country, $tags = [], $description)
    {
        $dbConnection = new DBConnection();
        $mysqli = $dbConnection->getDBConnection();

        if (is_null($mysqli)) {
            $_SESSION['error_message'] = "There was a problem connecting with the database";
            return false;
        }

        //check if recipe with that name has been created before
        $name = $mysqli->real_escape_string($name);
        $sql = "SELECT * FROM recipes WHERE name = '$name' AND id <> $recipeId";
        $result = $dbConnection->getSingleResultSet($sql);

        if (!empty($result)) {
            $_SESSION['error_message'] = "A recipe with that name has already been created";
            return false;
        }

        //Update the recipe with the appropriate recipe tags
        $country = $mysqli->real_escape_string($country);
        $description = $mysqli->real_escape_string($description);

        $sql = "UPDATE recipes
                    SET name = '$name',
                        description = '$description',
                        country = '$country',
                WHERE id = $recipeId";
        $result = $dbConnection->executeUpdate($sql);
        if (empty($result)) {
            $_SESSION['error_message'] = "Recipe update failed";
            return false;
        }

        //check whether tags need to be updated
        $recipeDetails = $this->getRecipeAndTags($recipeId);

        if (empty($recipeDetails)) {
            $_SESSION['error_message'] = "Invalid recipe ID supplied";
            return false;
        }

        $tags_in_db = array_column($recipeDetails, 'tag_id');
        sort($tags_in_db);
        $tags_submitted_during_update = [];
        foreach ($tags as $tag_id) {
            $tags_submitted_during_update[] = $tag_id;
        }
        sort($tags_submitted_during_update);

        if ($tags_in_db != $tags_submitted_during_update) {
            //update the tags
            //first delete existing ones then update the others
            $sql = "DELETE FROM tags WHERE recipeID = $recipeId";
            $dbConnection->executeUpdate($sql);

            //then insert the newly posted tags
            foreach ($tags_submitted_during_update as $tag_id) {
                $sql = "INSERT INTO recipe_tags (recipeID, tagID) VALUES ($recipeId, $tag_id)";
                $dbConnection->executeUpdate($sql);
            }
        }

        return true;
    }


    /**
     * @param $recipeId
     * @return array|bool
     */
    public function getRecipeAndTags($recipeId)
    {
        $sql = "SELECT
                    r.id AS recipe_id,
                    r.name AS recipe_name,
                    r.description,
                    r.country,
                    t.description AS tag_name,
                    t.id AS tag_id
                FROM
                    recipes r
                        LEFT JOIN
                    recipetags rt ON (r.id = rt.recipeID)
                        LEFT JOIN
                    tags t ON (t.id = rt.tagID)
                WHERE
                    r.id = $recipeId
                ";

        $dbConnection = new DBConnection();
        return $dbConnection->getMultipleResultSet($sql);
    }


    /**
     * @param $recipeId
     * @return array|bool
     */
    public function getRecipeOnly($recipeId)
    {
        $sql = "SELECT * FROM recipe WHERE id = $recipeId";
        $dbConnection = new DBConnection();
        return $dbConnection->getSingleResultSet($sql);
    }
}