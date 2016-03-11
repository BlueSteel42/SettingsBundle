<?php

namespace BlueSteel42\SettingsBundle\Service;

use BlueSteel42\SettingsBundle\Adapter\AdapterInterface;

class Settings
{

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Settings constructor.
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function get($name)
    {
        return $this->adapter->get($name);
    }

    public function set($name, $value)
    {
        $this->adapter->set($name, $value);

        return $this;
    }

    public function getAll()
    {
        return $this->adapter->getAll();
    }

}