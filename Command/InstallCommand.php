<?php


namespace BlueSteel42\SettingsBundle\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\Exception\RuntimeException;


class InstallCommand extends AbstractInstallCommand
{
    protected function configure()
    {
        $this
            ->setName('bluesteel42_settings:install')
            ->setDescription('Install SettingsBundle configurations enviroment.')
            ->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'Doctrine Connection')
            ->addOption('table_name', null, InputOption::VALUE_OPTIONAL, 'Table Name')
            ->addOption('sql-dump', null, InputOption::VALUE_NONE, 'Dump output query (no commit)')
            ->setHelp(<<<EOT
The <info>%command.name%</info> prepare configuration environment to starting to use SettingsBundle.
The <comment>connection</comment> parameter will set database connection to use.
The <comment>table_name</comment> parameter will set table name. If table already exixts, will be dropped and re-created.

Example 1 - With arguments:

./console <info>%command.name%</info> --connection=default --table_name=bluesteel42_settings

Example 2 - No arguments (console interaction needed):

./console <info>%command.name%</info>

Example 3 - With option

./console <info>%command.name%</info> --sql-dump

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $useQuestionCon = false;
        $useQuestionTable = false;
        $argConnection = $input->getOption('connection');
        $argTable = $input->getOption('table_name');
        $dump = $input->getOption('sql-dump');
        $connections = $this->getContainer()->getParameter('doctrine.connections');

        if (!$argConnection && !$argTable) {
            $con = ($this->getContainer()->getParameter('bluesteel42.settings.doctrinedbal.connection'));
            $tbl = ($this->getContainer()->getParameter('bluesteel42.settings.doctrinedbal.table'));
            $useQuestionCon = true;
            $useQuestionTable = true;
        } elseif ($argConnection && !$argTable) {
            $con = $argConnection;
            $tbl = ($this->getContainer()->getParameter('bluesteel42.settings.doctrinedbal.table'));
            $useQuestionTable = true;
        } elseif (!$argConnection && $argTable) {
            $con = ($this->getContainer()->getParameter('bluesteel42.settings.doctrinedbal.connection'));
            $useQuestionCon = true;
            $tbl = $argTable;
        } else {
            $con = $argConnection;
            $tbl = $argTable;
        }

        $conQuestion = new Question(sprintf('<question>Please select Doctrine Connection: [%s]</question>', $con), $con);
        $tblQuestion = new Question(sprintf('<question>Please select Table Name: [%s]</question>', $tbl), $tbl);
        $conAnswer = ($useQuestionCon) ? $this->getHelper('question')->ask($input, $output, $conQuestion) : $argConnection;
        $tblAnswer = ($useQuestionTable) ? $this->getHelper('question')->ask($input, $output, $tblQuestion) : $argTable;

        if (!array_key_exists($conAnswer, $connections)) {
            throw new \InvalidArgumentException(sprintf("Connection '%s' does not exist", $conAnswer));
        }

        /** @var Connection $conn */
        $conn = $this->get($connections[$conAnswer]);
        $sm = $conn->getSchemaManager();
        $tableExists = $sm->tablesExist($tblAnswer);

        $fromSchema = $sm->createSchema();
        $toSchema = clone $fromSchema;

        if (!$tableExists) {
            //  Table not Exists
            $this->createTableByToSchema($toSchema, $tblAnswer);
            $sqlQueryList = $fromSchema->getMigrateToSql($toSchema, $conn->getDatabasePlatform());
            $conn->beginTransaction();
            try {
                foreach ($sqlQueryList as $sql) {
                    if ($dump) {
                        $output->writeln($sql);
                    } else {
                        $conn->executeQuery($sql);
                        $conn->commit();
                        $output->writeln(sprintf("Table '%s' successfully created.", $tblAnswer));
                    }
                }
            } catch (\Exception $e) {
                $conn->rollBack();
                throw $e;
            }
        } else {
            //  Table not Exists
            $output->writeln(sprintf("Table '%s' already exists.", $tblAnswer));
            if ($dump) {
                //  Dump Only output
                $tmpSuffix = '_bs42_';
                $this->createTableByToSchema($toSchema, $tblAnswer . $tmpSuffix);
                $sqlQueryList = $fromSchema->getMigrateToSql($toSchema, $conn->getDatabasePlatform());
                foreach ($sqlQueryList as $sql) {
                    $sql = str_replace($tmpSuffix, '', $sql);
                    $output->writeln($sql);
                }
            } else {
                throw new RuntimeException(sprintf("Table '%s' on connection '%s' already exists.", $tblAnswer, $conAnswer));
            }

        }

    }

    /**
     * @param \Doctrine\DBAL\Schema\ $toSchema
     * @param string $tblAnswer
     * @return \Doctrine\DBAL\Schema\Table $myTable
     */
    protected function createTableByToSchema($toSchema, $tblAnswer)
    {
        $myTable = $toSchema->createTable($tblAnswer);
        $myTable->addColumn("id", "string", array("customSchemaOptions" => array("unique" => true)));
        $myTable->addColumn("val", "text");
        $myTable->addColumn("hasChildren", "boolean");
        $myTable->setPrimaryKey(array("id"));
        return $myTable;
    }
}