<?php

namespace BlueSteel42\SettingsBundle\Cache;

use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Umberto Stefani <umbe81@gmail.com>
 * @author Tonio Carta <plutonio21@gmail.com>
 */
class FileAdapter implements CacheInterface
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
     * @inheritDoc
     */
    public function getValues()
    {
        $values = unserialize($this->getFileContents());
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function setValues($values)
    {
        $this->setFileContents(serialize($values));
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getFileName()
    {
        return 'bluesteel42_settings.cache';
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
}