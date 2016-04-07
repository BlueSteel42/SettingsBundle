<?php
/**
 * Created by PhpStorm.
 * User: stefani
 * Date: 06/04/16
 * Time: 11.27
 */

namespace BlueSteel42\SettingsBundle\Cache;


/**
 * @author Umberto Stefani <umbe81@gmail.com>
 * @author Tonio Carta <plutonio21@gmail.com>
 */
class MemcachedAdapter implements CacheInterface
{

    const PREFIX = 'bluesteel42_settings';
    protected $memcached;

    public function __construct()
    {
        $this->memcached = new \Memcached();
    }

    /**
     * @inheritDoc
     */
    public function getValues()
    {
       $ret = $this->memcached->get(self::PREFIX);
       if($this->memcached->getResultCode() == \Memcached::RES_NOTFOUND){
           return null;
       }
       return $ret;
    }

    /**
     * @inheritDoc
     */
    public function setValues($values)
    {
        $this->memcached->set(self::PREFIX, $values, CacheInterface::LIFETIME);
        return $this;
    }

    /**
     * @param $host
     * @param $port
     * @return $this
     */
    public function addServer($host, $port)
    {
        $this->memcached->addServer($host, $port);
        return $this;
    }


}