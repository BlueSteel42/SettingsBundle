<?php

namespace BlueSteel42\SettingsBundle\Adapter;

/**
 * @author Umberto Stefani <umbe81@gmail.com>
 * @author Tonio Carta <plutonio21@gmail.com>
 */

interface AdapterInterface
{
    /**
     * @param string $name Configuration key
     * @return mixed
     */
    public function get($name);

    /**
     * @param string $name Configuration key
     * @param mixed $value Configuration value
     * @return AdapterInterface
     */
    public function set($name, $value);

    /**
     * @param $name
     * @return AdapterInterface
     */
    public function delete($name);

    /**
     * @return array
     */
    public function getAll();

    /**
     * @param array $values Full array of values to be set
     * @return mixed
     */
    public function setAll(array $values);

    /**
     * @return AdapterInterface
     */
    public function flush();
}