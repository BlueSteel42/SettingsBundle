<?php


namespace BlueSteel42\SettingsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Config\FileLocator;

class InstallCommand extends AbstractInstallCommand
{
    protected function configure()
    {
        $this
            ->setName('bluesteel42:install')
            ->setDescription('Install SettingsBundle configurations enviroment.')
            ->addOption('mysql-dump', null, InputOption::VALUE_NONE, 'Dump output query (no commit)')
            ->setHelp(<<<EOT
The <info>%command.name%</info> prepare configuration enviroment to starting to use SettingsBundle.

For example, ./console <info>%command.name%</info> --mysql-dump

will simulate install dumping mysql query in output.

./console <info>%command.name%</info> will proceed with installation instead.

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dump = $input->getOption('mysql-dump');
        $yml = new Parser();
        $cfg = null;
        try {
            $cfg = $yml->parse(file_get_contents(__DIR__ . '/../../../../app/config/config.yml'));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }

        if (array_key_exists('bluesteel42_settings', $cfg)) {

            if ($cfg['bluesteel42_settings']['backend'] == 'yml' || $cfg['bluesteel42_settings']['backend'] == 'xml') {
                $ext = $cfg['bluesteel42_settings']['backend'];
                $locator = new FileLocator($this->getContainer()->getParameter(sprintf('bluesteel42.settings.%s.path', $ext)));
                $cfgFile = $locator->locate(sprintf('bluesteel42_settings.%s', $ext), sprintf($this->getContainer()->getParameter(sprintf('bluesteel42.settings.%s.path', $ext), false)));
            }

            $con = ($this->getContainer()->getParameter('bluesteel42.settings.doctrine.connection'));
            $tbl = ($this->getContainer()->getParameter('bluesteel42.settings.doctrine.table'));

            $conQuestion = new Question(sprintf('<question>Please select Doctrine Connection: [%s]</question>', $con), $con);
            $conAnswer = $this->getHelper('question')->ask($input, $output, $conQuestion);

            $tblQuestion = new Question(sprintf('<question>Please select Table Name: [%s]</question>', $tbl), $tbl);
            $tblAnswer = $this->getHelper('question')->ask($input, $output, $tblQuestion);

            $em = $this->getContainer()->get('doctrine')->getEntityManager($conAnswer);
            $conn = $em->getConnection();
            $sm = $conn->getSchemaManager();

            $tableExists = $sm->tablesExist($tblAnswer);
            if (!$tableExists) {
                $conn->beginTransaction();
                try{
                    $fromSchema = $sm->createSchema();
                    $toSchema = clone $fromSchema;

                    $myTable = $toSchema->createTable($tblAnswer);
                    $myTable->addColumn("id", "string", array("customSchemaOptions" => array("unique" => true)));
                    $myTable->addColumn("val", "text");
                    $myTable->setPrimaryKey(array("id"));

                    $sqlQueryList = $fromSchema->getMigrateToSql($toSchema, $conn->getDatabasePlatform());
                    foreach ($sqlQueryList as $sql) {
                        if ($dump) {
                            $output->writeln(sprintf("<comment>%s</comment>", $sql));
                        } else {
                            $conn->executeQuery($sql);
                            $conn->commit();
                            $output->writeln(sprintf("Table %s successfuly created.", $tblAnswer));
                        }
                    }
                } catch(\Exception $e) {
                    $conn->rollBack();
                    throw $e;
                }
            } else {
                if($dump) {
                    $sql = sprintf("CREATE TABLE %s (id VARCHAR(255) NOT NULL UNIQUE, val LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB", $tblAnswer);
                    $output->writeln(sprintf("<comment>%s</comment>", $sql));
                } else {
                    throw new \RuntimeException(sprintf("Table %s already exists.", $tblAnswer));
                }
            }
        } else {
            throw new \RuntimeException("Configuration key bluesteel42_settings not found.");
        }
    }
}