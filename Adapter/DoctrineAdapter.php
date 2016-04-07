<?php

namespace BlueSteel42\SettingsBundle\Adapter;

use Doctrine\DBAL\Connection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Umberto Stefani <umbe81@gmail.com>
 * @author Tonio Carta <plutonio21@gmail.com>
 */
class DoctrineAdapter extends AbstractAdapter
{

    #   http://doctrine-orm.readthedocs.org/projects/doctrine-dbal/en/latest/reference/data-retrieval-and-manipulation.html

    /**
     * @var RegistryInterface
     */
    protected $doctrine;
    /**
     * @var Connection
     */
    protected $connection;
    /**
     * @var object
     */
    protected $conn;
    /**
     * @var string
     */
    protected $tableName;
    /**
     * @var array
     */
    protected $values;

    const CHILD_SEPARATOR = '.';


    /**
     * DoctrineAdapter constructor.
     * @param RegistryInterface $doctrine
     * @param $connection
     * @param $tableName
     */
    public function __construct(RegistryInterface $doctrine, $connection, $tableName)
    {
        $this->doctrine = $doctrine;
        $this->connection = $connection;
        $this->conn = $doctrine->getConnection($connection);
        $this->tableName = $tableName;
    }

    /**
     * @param \Doctrine\DBAL\Schema\ $toSchema
     * @param string $tbl
     * @return \Doctrine\DBAL\Schema\Table $myTable
     */
    public static function createTableByToSchema($toSchema, $tbl)
    {
        $myTable = $toSchema->createTable($tbl);
        $myTable->addColumn("id", "string", array("customSchemaOptions" => array("unique" => true)));
        $myTable->addColumn("val", "text", array("notnull" => false));
        $myTable->addColumn("hasChildren", "boolean");
        $myTable->setPrimaryKey(array("id"));
        return $myTable;
    }

    /**
     * @inheritdoc
     */
    protected function doGetValues()
    {

        $serialized = array();
        $sql = sprintf("SELECT * FROM %s ORDER BY id ASC", $this->tableName);
        $result = $this->conn->fetchAll($sql);

        foreach ($result as $k => $values) {

            $root = $values['id'];
            if ((int)$values['hasChildren'] == 1) {
                $serialized[$root] = array();
            } else {
                $this->assignArrayByPath($serialized, $values['id'], $values['val']);
            }
        }

        ksort($serialized, SORT_STRING);
        return $serialized;
    }

    /**
     * @return AdapterInterface
     * @throws \Exception
     */
    protected function doFlush()
    {
        $this->conn->beginTransaction();
        try {

            //  Clear table
            $sqlClearTable = sprintf("DELETE FROM %s", $this->tableName);
            $this->conn->executeQuery($sqlClearTable);

            $converted = $this->convertArray($this->getValues());
            ksort($converted);

            foreach ($converted as $k => $current) {
                $insert = array("id" => $k, "val" => ($current["val"] == '') ? null : $current["val"], "hasChildren" => $current["hasChildren"]);
                $this->conn->insert($this->tableName, $insert);
            }
            $this->conn->commit();

        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
        return $this;
    }


    /**
     * Function to convert an array in a query-set ready to lunch
     * @param array $arr
     * @param array $narr
     * @param string $nkey
     * @return array array
     */
    protected function convertArray($arr, $narr = array(), $nkey = '') {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $narr[$key]['val'] = null;
                $narr[$key]['hasChildren'] = 1;
                $narr = array_merge($narr, $this->convertArray($value, $narr, $nkey . $key . '.'));
            } else {
                $narr[$nkey . $key]['val'] = $value;
                $narr[$nkey . $key]['hasChildren'] = 0;
            }
        }
        return $narr;
    }

    /**
     * Function to serialize keys from database in tree-structured-array
     * @param array $arr
     * @param string $path
     * @param string $value
     * @param string $separator
     */
    protected function assignArrayByPath(&$arr, $path, $value, $separator = self::CHILD_SEPARATOR)
    {
        $keys = explode($separator, $path);

        foreach ($keys as $key) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }

}