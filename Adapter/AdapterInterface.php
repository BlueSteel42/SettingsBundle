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
     * @return mixed
     */
    public function get($name);

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function set($name, $value);

    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @return mixed
     */
    public function setAll();
}