<?php


namespace BlueSteel42\SettingsBundle\Cache;

/**
 * @author Umberto Stefani <umbe81@gmail.com>
 * @author Tonio Carta <plutonio21@gmail.com>
 */
class NullAdapter implements CacheInterface
{

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setValues($values)
    {
        return $this;
    }
}