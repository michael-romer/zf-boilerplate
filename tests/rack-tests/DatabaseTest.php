<?php

class DatabaseTest extends phpRack_Test
{
    public function testConnectionIsAlive()
    {
        $config = Zend_Registry::get('config');

        $host = $config['resources']['doctrine']['dbal']['connections']['default']['parameters']['host'];
        $port = $config['resources']['doctrine']['dbal']['connections']['default']['parameters']['port'];
        $username = $config['resources']['doctrine']['dbal']['connections']['default']['parameters']['user'];
        $password = $config['resources']['doctrine']['dbal']['connections']['default']['parameters']['password'];
        $dbname = $config['resources']['doctrine']['dbal']['connections']['default']['parameters']['dbname'];

        $this->assert->db->mysql
            ->connect($host, $port, $username, $password)
            ->dbExists($dbname);
    }

    public function testPhpExtensionsExist()
    {
        $this->assert->php->extensions
            ->isLoaded('pdo');
    }
}