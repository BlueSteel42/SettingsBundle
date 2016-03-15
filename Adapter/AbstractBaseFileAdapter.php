<?php

namespace BlueSteel42\SettingsBundle\Adapter;

use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;


abstract class AbstractBaseFileAdapter extends AbstractAdapter
{
    /**
     * @var FileLocator
     */
    protected $locator;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $path;

    public function __construct(FileLocator $locator, $path)
    {
        $this->locator = $locator;
        $this->fs = new Filesystem();
        $this->path = $this->locator->locate($path);
    }

    /**
     * @return string
     */
    protected function getFileContents()
    {
        $filename = $this->path . DIRECTORY_SEPARATOR . $this->getFileName();

        if (!$this->fs->exists($filename)) {
            return '';
        }

        return file_get_contents($filename);
    }

    /**
     * @param $content
     * @return AbstractBaseFileAdapter
     */
    protected function setFileContents($content)
    {
        $filename = $this->path . DIRECTORY_SEPARATOR . $this->getFileName();
        $this->fs->dumpFile($filename, $content);

        return $this;
    }

    /**
     * @return string
     */
    protected abstract function getFileName();
}