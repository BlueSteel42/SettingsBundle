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
     * @var bool
     */
    protected $throwExceptions = false;

    /**
     * Settings constructor.
     * @param AdapterInterface $adapter
     * @param bool $exceptions
     */
    public function __construct(AdapterInterface $adapter, $exceptions)
    {
        $this->adapter = $adapter;
        $this->throwExceptions = $exceptions;
    }

    public function get($name)
    {
        if ($this->throwExceptions) {
            return $this->adapter->get($name);
        } else {
            try {
                return $this->adapter->get($name);
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    public function set($name, $value)
    {
        $this->adapter->set($name, $value);

        return $this;
    }

    public function delete($name)
    {
        if ($this->throwExceptions) {
            $this->adapter->delete($name);
        } else {
            try {
                $this->adapter->delete($name);
            } catch (\Exception $e) {

            }
        }

        return $this;
    }

    public function getAll()
    {
        if ($this->throwExceptions) {
            return $this->adapter->getAll();
        } else {
            try {
                return $this->adapter->getAll();
            } catch (\Exception $e) {
                return array();
            }
        }
    }

    public function setAll(array $values) {
        $this->adapter->setAll($values);

        return $this;
    }

    public function flush()
    {
        $this->adapter->flush();

        return $this;
    }

    /**
     * @return boolean
     */
    public function getThrowExceptions()
    {
        return $this->throwExceptions;
    }

    /**
     * @param boolean $throwExceptions
     * @return Settings
     */
    public function setThrowExceptions($throwExceptions)
    {
        $this->throwExceptions = (bool)$throwExceptions;

        return $this;
    }

}