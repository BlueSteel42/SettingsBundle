<?php

namespace BlueSteel42\SettingsBundle\Adapter;

/**
 * @author Umberto Stefani <umbe81@gmail.com>
 * @author Tonio Carta <plutonio21@gmail.com>
 */

interface AdapterInterface
{
    /**
     * @param $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null);

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
     * @return AdapterInterface
     */
    public function setAll(array $values);

    /**
     * Flush changes
     *
     * @return AdapterInterface
     */
    public function flush();
}