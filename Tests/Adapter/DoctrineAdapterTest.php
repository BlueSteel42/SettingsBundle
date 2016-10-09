<?php

namespace BlueSteel42\SettingsBundle\Tests\Adapter;

use BlueSteel42\SettingsBundle\Adapter\DoctrineAdapter;

class DoctrineAdapterTest extends BaseAdapterTest
{
    protected $env = 'doctrinedbal';

    public function setUp()
    {
        parent::setUp();

        $container = $this->getKernel('doctrinedbal')->getContainer();
        $this->connection = $container->getParameter('bluesteel42.settings.doctrinedbal.connection');
        $this->table_name = $container->getParameter('bluesteel42.settings.doctrinedbal.table');

        $conn = $container->get($container->getParameter('doctrine.connections')[$this->connection]);
        $sm = $conn->getSchemaManager();
        $fromSchema = $sm->createSchema();
        $toSchema = clone $fromSchema;
        $tableExists = $sm->tablesExist($this->table_name);

        if (!$tableExists) {
            DoctrineAdapter::createTableByToSchema($toSchema, $this->table_name);
            $sqlQueryList = $fromSchema->getMigrateToSql($toSchema, $conn->getDatabasePlatform());
            foreach ($sqlQueryList as $sql) {
                $conn->executeQuery($sql);
            }
        }

    }

}