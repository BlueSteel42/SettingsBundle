<?php

namespace BlueSteel42\SettingsBundle\Tests;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use BlueSteel42\SettingsBundle\Command\InstallCommand;

class InstallCommandTest extends TestCase
{
    protected $table_name;
    protected $connection;


    public function setUp()
    {
        parent::setUp();
        $container = $this->getKernel('doctrinedbal')->getContainer();
        $this->connection = $container->getParameter('bluesteel42.settings.doctrinedbal.connection');
        $this->table_name = $container->getParameter('bluesteel42.settings.doctrinedbal.table');

        /**
         * @var Connection $conn
         */
        $conn = $container->get($container->getParameter('doctrine.connections')[$this->connection]);
        $sm = $conn->getSchemaManager();

        if (in_array($this->table_name, $sm->listTableNames())) {
            $from = $sm->createSchema();
            $to = clone $from;
            $to->dropTable($this->table_name);
            $sqlQueryList = $from->getMigrateToSql($to, $conn->getDatabasePlatform());
            foreach ($sqlQueryList as $sql) {
                $conn->executeQuery($sql);
            }
        }
    }




    public function testInstall()
    {

        list($command, $commandTester) = $this->createCommand();
        $commandTester->execute(array('command' => $command->getName(), '--connection' => $this->connection, '--table_name' => $this->table_name));

        $out = sprintf("Table '%s' successfully created.", $this->table_name);
        $this->assertRegExp(sprintf("/^%s/", $out), $commandTester->getDisplay());
    }

    public function testInstallSqlDump()
    {

        list($command, $commandTester) = $this->createCommand();
        $commandTester->execute(array('command' => $command->getName(), '--connection' => $this->connection, '--table_name' => $this->table_name, '--sql-dump' => true));

        $sql = sprintf("CREATE TABLE %s", $this->table_name);
        $this->assertRegExp(sprintf("/^%s/", $sql), $commandTester->getDisplay());
    }

    /**
     * @return array
     */
    protected function createCommand()
    {
        $this->kernels['doctrinedbal'] = null;
        $kernel = $this->getKernel('doctrinedbal');
        $application = new Application();
        $application->add(new InstallCommand());
        $command = $application->find('bluesteel42_settings:install');
        $command->setContainer($kernel->getContainer());
        $commandTester = new CommandTester($command);
        return array($command, $commandTester);
    }

}