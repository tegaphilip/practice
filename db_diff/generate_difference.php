#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: Abiola
 * Date: 4/9/14
 * Time: 10:20 AM
 */

require_once(realpath(dirname(__FILE__)) . '/dbdiff.php');

/**
 * The first configuration is the target configuration.
 * e.g. DbDiff('staging', 'live') will make 'live' database to match the structure in 'staging'
 */

if(count($argv) <= 1) {
    echo "Command supplied without arguments\n";
    echo "Type help as an argument for instructions\n";
    die;
}

if($argv[1] == "help") {
    echo "To show database difference, run with arguments dbconfig1 dbconfig2";
    die;
}

if($argv[1] != "help" && !isset($argv[2])) {
    echo "Invalid command\n";
    echo "Type help as an argument for instructions\n";
    die;
}

if(isset($argv[1], $argv[2])) {
    $diff = new DbDiff($argv[1], $argv[2]);
    $sql = $diff->generateDiff();
    if(trim($sql) == "") {
        echo "No differences between databases.\n\n";
    } else {
        $sql = "SET FOREIGN_KEY_CHECKS = 0;\n" . $sql . "\nSET FOREIGN_KEY_CHECKS = 1;";
        $fileName = "SQL_Migrate_from_".$diff->getName1()."_to_".$diff->getName2()."_".date("j.n.Y") . '.sql';
        $filePath = dirname(__FILE__).DIRECTORY_SEPARATOR.$fileName;
        echo "Generating SQL required to migrate >> " . $diff->getName2() . " to match >>" . $diff->getName1() . "\n";
        file_put_contents(
            $fileName,
            $sql
        );
        echo "SQL has been written to file\n".$filePath."\n";
        echo "==================================================================================++++++++++++\n\n";
    }
}

return;