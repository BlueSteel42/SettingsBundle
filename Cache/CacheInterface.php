<?php

namespace BlueSteel42\SettingsBundle\Cache;

/**
 * @author Umberto Stefani <umbe81@gmail.com>
 * @author Tonio Carta <plutonio21@gmail.com>
 */

interface CacheInterface
{

    //  Maximum value allowed by memcached
    const LIFETIME = 2592000;

    /**
     * @return mixed
     */
    public function getValues();

    /**
     * @param $values
     * @return mixed
     */
    public function setValues($values);


}