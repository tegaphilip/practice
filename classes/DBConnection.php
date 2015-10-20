<?php

class DBConnection
{
    /**
     * @return mysqli
     */
    public function getDBConnection()
    {
        $db_host = '127.0.0.1';
        $db_login = 'root';
        $db_password = '';
        $db_name = 'recipe_db';

        $mysqli = new mysqli($db_host,$db_login,$db_password,$db_name);

        if ($mysqli->connect_errno) {
            die("There was an error connecting to the database");
        } else {
            return $mysqli;
        }
    }

    /**
     * @param $sql
     * @return array|bool
     */
    public function getSingleResultSet($sql)
    {
        $mysqli = $this->getDBConnection();

        $result = $mysqli->query($sql);
        if (empty($result) || $result->num_rows == 0) {
            return false;
        }
        return $result->fetch_assoc();
    }

    /**
     * @param $sql
     * @return int
     */
    public function executeUpdate($sql)
    {
        $mysqli = $this->getDBConnection();
        $mysqli->query($sql);
        return $mysqli->affected_rows;
    }

    /**
     * @param $sql
     * @return bool|mixed
     */
    public function executeInsert($sql)
    {
        $mysqli = $this->getDBConnection();
        $mysqli->query($sql);
        if ($mysqli->affected_rows > 0) {
            return $mysqli->insert_id;
        }
        return false;
    }


    /**
     * @param $sql
     * @return array|bool
     */
    public function getMultipleResultSet($sql)
    {
        $resultSet = [];

        $mysqli = $this->getDBConnection();

        $result = $mysqli->query($sql);
        if (!empty($result) && $result->num_rows != 0) {
            while ($row = $result->fetch_assoc()) {
                $resultSet[] = $row;
            }
        }

        return $resultSet;
    }

    /**
     * @return mixed
     */
    public function getLastInsertID()
    {
        $mysqli = $this->getDBConnection();
        return $mysqli->insert_id;
    }
}