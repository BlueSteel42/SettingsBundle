<?php


namespace BlueSteel42\SettingsBundle\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;


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
The <comment>table_name</comment> parameter will set table name.

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

        $yml = new Parser();
        $cfg = null;
        try {
            $cfg = $yml->parse(file_get_contents(__DIR__ . '/../../../../app/config/config.yml'));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }

        if (array_key_exists('bluesteel42_settings', $cfg)) {

//            if ($cfg['bluesteel42_settings']['backend'] == 'yml' || $cfg['bluesteel42_settings']['backend'] == 'xml') {
//                $ext = $cfg['bluesteel42_settings']['backend'];
//                $locator = new FileLocator($this->getContainer()->getParameter(sprintf('bluesteel42.settings.%s.path', $ext)));
//                $cfgFile = $locator->locate(sprintf('bluesteel42_settings.%s', $ext), $this->getContainer()->getParameter(sprintf('bluesteel42.settings.%s.path', $ext), false));
//            }

            $conQuestion = new Question(sprintf('<question>Please select Doctrine Connection: [%s]</question>', $con), $con);
            $tblQuestion = new Question(sprintf('<question>Please select Table Name: [%s]</question>', $tbl), $tbl);
            $conAnswer = ($useQuestionCon) ? $this->getHelper('question')->ask($input, $output, $conQuestion) : $argConnection;
            $tblAnswer = ($useQuestionTable) ? $this->getHelper('question')->ask($input, $output, $tblQuestion) : $argTable;

            if (!array_key_exists($conAnswer, $connections)) {
                throw new \InvalidArgumentException(sprintf('The connection %s does not exist', $conAnswer));
            }
            /** @var Connection $conn */
            $conn = $this->get($connections[$conAnswer]);
            $sm = $conn->getSchemaManager();
            $tableExists = $sm->tablesExist($tblAnswer);

            $conn->beginTransaction();
            if (!$tableExists) {
                try {
                    $fromSchema = $sm->createSchema();
                    $toSchema = clone $fromSchema;
                    $myTable = $toSchema->createTable($tblAnswer);
                    $myTable->addColumn("id", "string", array("customSchemaOptions" => array("unique" => true)));
                    $myTable->addColumn("val", "text");
                    $myTable->addColumn("hasChildren", "boolean");
                    $myTable->setPrimaryKey(array("id"));
                    $sqlQueryList = $fromSchema->getMigrateToSql($toSchema, $conn->getDatabasePlatform());
                    foreach ($sqlQueryList as $sql) {
                        if ($dump) {
                            $output->writeln($sql);
                        } else {
                            $conn->executeQuery($sql);
                            $conn->commit();
                            $output->writeln(sprintf("Table %s successfully created.", $tblAnswer));
                        }
                    }
                } catch (\Exception $e) {
                    $conn->rollBack();
                    throw $e;
                }

            } else {
                if ($dump) {
                    $sql = sprintf("CREATE TABLE %s (id VARCHAR(255) NOT NULL UNIQUE, val LONGTEXT NOT NULL, hasChildren TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB", $tblAnswer);
                    $output->writeln(sprintf("<comment>%s</comment>", $sql));
                } else {
                    $conn->rollBack();
                    throw new \RuntimeException(sprintf("Table %s already exists.", $tblAnswer));
                }
            }
        } else {
            throw new \RuntimeException("Configuration key bluesteel42_settings not found.");
        }
    }
}