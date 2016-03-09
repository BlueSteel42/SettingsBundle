<?php

namespace BlueSteel42\SettingsBundle\Adapter;

use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * @author Umberto Stefani <umbe81@gmail.com>
 * @author Tonio Carta <plutonio21@gmail.com>
 */

class XmlAdapter implements AdapterInterface
{

    protected $locator;

    protected $path;

    public function __construct(FileLocator $locator, $path)
    {
        $this->locator = $locator;

        $this->path = $path;
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