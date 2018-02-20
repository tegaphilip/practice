<?php

/**
 * This file should contain the configuration of databases.
 *
 * $dbs_config is an array of database configurations. Each element of the
 * array should provide details for a database which will be selectable from
 * a list.
 *
 * Refer to the 'Demo Configuration' below for reference.
 *
 * $dbs_config = array(
 *      array(
 *          'name' => 'Demo Configuration',
 *          'config' => array(
 *              'host'        => 'localhost',
 *              'user'        => 'db_user',
 *              'password'    => 'db_password',
 *              'db_name'     => 'db_name'
 *          )
 *      ),
 * );
 *
 */
class Config
{

    private $db_config = array(

        array(
            'name' => 'hocaboo_test',
            'config' => array(
                'host' => '127.0.0.1',
                'user' => 'root',
                'password' => '',
                'db_name' => 'hocaboo_test_db'
            )
        ),
        array(
            'name' => 'hocaboo_api',
            'config' => array(
                'host' => '127.0.0.1',
                'user' => 'root',
                'password' => '',
                'db_name' => 'hocaboo_api'
            )
        ),
    );

    public static function getDBConfig($name)
    {
        $class = new Config();
        foreach ($class->db_config as $config) {
            if ($config['name'] == $name) {
                return $config['config'];
            }
        }
        return false;
    }

}