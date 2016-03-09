<?php

namespace BlueSteel42\SettingsBundle\Adapter;

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Umberto Stefani <umbe81@gmail.com>
 * @author Tonio Carta <plutonio21@gmail.com>
 */

class DoctrineAdapter implements AdapterInterface
{

    protected $doctrine;
    protected $connection;
    protected $tableName;

    public function __construct(RegistryInterface $doctrine, $connection, $tableName)
    {
        $this->doctrine = $doctrine;
        $this->connection = $connection;
        $this->tableName = $tableName;
    }

    /**
     * @inheritDoc
     */
    public function get($name)
    {
        // TODO: Implement get() method.
    }

    /**
     * @inheritDoc
     */
    public function set($name, $value)
    {
        // TODO: Implement set() method.
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        // TODO: Implement getAll() method.
    }

    /**
     * @inheritDoc
     */
    public function setAll()
    {
        // TODO: Implement setAll() method.
    }


}