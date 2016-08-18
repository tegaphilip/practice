<?php

class DbDiff
{


    private $conn1;
    private $conn2;
    private $name1;
    private $name2;
    private $createCount;
    private $alterCount;

    /**
     * Initialize Class
     * @param $name1 string config name to use for first/target db
     * @param $name2 string config name to use for second/comparison db
     */
    public function __construct($name1, $name2)
    {
        require_once(realpath(dirname(__FILE__)) . '/config.php');
        $config1 = Config::getDBConfig($name1);
        $config2 = Config::getDBConfig($name2);
        if($config1 && $config2) {
            $this->conn1 = new mysqli($config1['host'], $config1['user'], $config1['password'], $config1['db_name']);
            if ($this->conn1->connect_errno)
            {
                die($this->conn1->connect_error . "\n");
            }
            $this->conn2 = new mysqli($config2['host'], $config2['user'], $config2['password'], $config2['db_name']);
            if ($this->conn2->connect_errno)
            {
                die($this->conn2->connect_error . "\n");
            }
            $this->name1 = $name1;
            $this->name2 = $name2;
        } else {
            die("One or more of the arguments supplied is not a valid configuration");
        }
    }

    /**
     * Perform Database Migration
     */
    public function doMigration()
    {
        echo "\tUpdating $this->name2 to match $this->name1...";
        $sql = $this->generateDiff();
        if (trim($sql) == '')
        {
            die("\n\tNo difference between databases. Migration terminated.\n");
        }
        $sql = "SET FOREIGN_KEY_CHECKS = 0;\n" . $sql . "\nSET FOREIGN_KEY_CHECKS = 1;";
        $this->conn2->multi_query($sql);
        //Loop through executions to trace errors
        while ($this->conn2->next_result())
        {
            $result = $this->conn2->store_result();
            echo "......";
            if ($result)
            {
                $result->close();
            }
        }
        if ($this->conn2->errno)
        {
            die("\n\tMigration failed while updating. \nError: " . mysqli_error($this->conn2) . "\n");
        }
        echo "\n\tMigration Successful!\n";
        echo "\t$this->createCount tables created.\n";
        echo "\t$this->alterCount tables altered.\n";
    }

    /**
     * Generates the SQL statement required to alter DB2 to match DB1
     */
    public function generateDiff()
    {

        $schema1 = $this->getTables($this->conn1);
        $schema2 = $this->getTables($this->conn2);

        //store tables to create
        $toCreate = array();

        //store tables to alter
        $toAlter = array();

        //check for tables to create
        $this->createCount = 0;
        foreach ($schema1 as $table)
        {
            if (!in_array($table, $schema2))
            {
                $toCreate[] = $table;
                $this->createCount++;
            } else
            {
                //check for tables to alter
                if (!$this->tableIsEqual($table))
                {
                    $toAlter[] = $table;
                }
            }
        }

        //generate all scripts to create required tables
        $createQueries = $this->genCreateScripts($toCreate);

        $alterQueries = "";

        $this->alterCount = 0;
        foreach ($toAlter as $table)
        {
            $query = "\n\nALTER TABLE `$table` ";
            //Columns to be added
            $alterAdd = $this->getMissingCols($table);
            $query .= $this->genColAddScripts($table, $alterAdd);
            //Columns to be modified
            $alterMod = $this->colAlterStmt($table);
            $q = $this->genColModifyScripts($alterMod);
            if ($q)
            {
                if ($alterAdd)
                {
                    $query .= ', ';
                }
                $query .= $q;
            }
            $query .= ";";
            $alterQueries .= $query;
            $this->alterCount++;
        }

        //Merge CREATE queries with ALTER queries

        return $createQueries . $alterQueries;


    }

    /**
     * Generate SQL scripts to create missing Tables
     * @param $tables
     * @return string
     */
    private function genCreateScripts($tables)
    {
        $query = '';
        foreach ($tables as $table)
        {
            $query .= $this->getTableStmt($this->conn1, $table) . "; \n";
        }
        return $query;
    }

    /**
     * Generate scripts for columns to add with ALTER statement
     * @param $table
     * @param $columns
     * @return string
     */
    private function genColAddScripts($table, $columns)
    {
        $query = "";
        $i = 0;
        foreach ($columns as $col)
        {
            $stmt = $this->getTableColStmt($this->conn1, $table, $col);
            if ($stmt)
            {
                $query .= "\nADD `$col` $stmt";
                if ($i < count($columns) - 1)
                {
                    $query .= ",";
                }
            }
            $i++;
        }
        return $query;
    }


    /**
     * Generate scripts for columns to modify with ALTER statement
     * @param $stmts
     * @return mixed
     */
    private function genColModifyScripts($stmts)
    {
        $query = "";
        $i = 0;
        foreach ($stmts as $name => $stmt)
        {
            $query .= "\nCHANGE `$name` `$name` $stmt";
            if ($i < count($stmts) - 1)
            {
                $query .= ',';
            }
            $i++;
        }
        return $query;
    }

    /**
     * Retrieve tables in a database
     * @param $conn
     * @return array
     */
    private function getTables($conn)
    {
        $result = $conn->query("SHOW TABLES");
        $empty = array();
        if (!$result)
        {
            return $empty;
        }
        $tables = array();
        while ($row = $result->fetch_row())
        {
            $tables[] = $row[0];
        }
        return $tables;
    }

    /**
     * Check if table is equal in both databases
     * @param $table string table name
     * @return bool
     */
    private function tableIsEqual($table)
    {
        //check columns
        if ($this->getMissingCols($table))
        {
            return false;
        }

        //check structure
        $table1 = $this->getTableStmt($this->conn1, $table);
        $table2 = $this->getTableStmt($this->conn2, $table);
        return $this->tblStmtHash($table1) == $this->tblStmtHash($table2);
    }

    /**
     * Get missing Cols between two databases
     * @param $table
     * @return array
     */
    private function getMissingCols($table)
    {
        $columns1 = $this->getColumns($this->conn1, $table);
        $columns2 = $this->getColumns($this->conn2, $table);
        $columns = array();
        foreach ($columns1 as $col)
        {
            if (!in_array($col, $columns2))
            {
                $columns[] = $col;
            }
        }
        return $columns;
    }

    private function getColumns($conn, $table)
    {
        $sql = "SHOW COLUMNS IN $table";
        $result = $conn->query($sql);
        $columns = array();
        while ($row = $result->fetch_row())
        {
            $columns[] = $row[0];
        }
        return $columns;
    }

    /**
     * Get create statement for a table
     * @param $conn
     * @param $table
     * @return string
     */
    private function getTableStmt($conn, $table)
    {
        $sql = "SHOW CREATE TABLE $table";
        $result = $conn->query($sql);
        $str = $result->fetch_array();
        $str = $str['Create Table'];
        $str = $this->trimTblStr($str);
        return $str;
    }

    private function trimTblStr($str)
    {
        $str = substr($str, 0, strrpos($str, ')') + 1);
        $str = trim($str);
        return $str;
    }

    /**
     * Hash table query to do comparison.
     * @param $stmt
     * @return string
     */
    private function tblStmtHash($stmt)
    {
        $strs = explode(",\n", $stmt);
        $tmp = array();
        foreach ($strs as $str)
        {
            $tmp[] = md5($str);
        }
        sort($tmp);
        return implode("", $tmp);
    }

    /**
     * Get the sql statement to craete column
     * @param $conn
     * @param $table
     * @param $col the column to return
     * @return bool
     */
    private function getTableColStmt($conn, $table, $col)
    {
        $str = $this->getTableStmt($conn, $table);
        $i = strpos($str, '(') + 1;
        $j = strrpos($str, ')') - $i;
        $str = substr($str, $i, $j);
        $strs = explode(",\n", $str);
        foreach ($strs as $s)
        {
            $s = trim($s);
            if (strpos($s, '`') != 0)
            {
                continue;
            }
            $pos = strpos($s, '`') + 1;
            $pos1 = strpos($s, '`', $pos);
            $stmt = trim(substr($s, $pos1 + 1));
            $colName = trim(substr($s, 1, $pos1 - 1));
            if ($colName == $col)
            {
                return $stmt;
            }
        }
        return false;
    }

    /**
     * Get SQL to
     * @param $table
     * @return array
     */
    private function colAlterStmt($table)
    {
        $cols1 = $this->getTableColsStmt($this->conn1, $table);
        $cols2 = $this->getTableColsStmt($this->conn2, $table);
        $stmts = array();
        foreach ($cols1 as $name => $stmt)
        {
            if (isset($cols2[$name]) && $cols2[$name] !== $stmt)
            {
                $stmts[$name] = $stmt;
            }
        }
        return $stmts;
    }

    /**
     * Get sql statement to create multiple columns
     * @param $conn
     * @param $table
     * @return array key is column name, value is sql script
     */
    private function getTableColsStmt($conn, $table)
    {
        $str = $this->getTableStmt($conn, $table);
        $i = strpos($str, '(') + 1;
        $j = strrpos($str, ')') - $i;
        $str = substr($str, $i, $j);
        $strs = explode(",\n", $str);
        $cols = array();
        foreach ($strs as $s)
        {
            $s = trim($s);
            if (strpos($s, '`') > 0)
            {
                continue;
            }
            $pos = strpos($s, '`') + 1;
            $pos1 = strpos($s, '`', $pos);
            $stmt = trim(substr($s, $pos1 + 1));
            $colName = trim(substr($s, 1, $pos1 - 1));
            $cols[$colName] = $stmt;
        }
        return $cols;
    }

    public function getName1()
    {
        return $this->name1;
    }

    public function getName2()
    {
        return $this->name2;
    }

}
