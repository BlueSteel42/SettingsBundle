<?php

namespace BlueSteel42\SettingsBundle\Adapter;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;


/**
 * @author Umberto Stefani <umbe81@gmail.com>
 * @author Tonio Carta <plutonio21@gmail.com>
 */
class YmlAdapter extends AbstractBaseFileAdapter
{


    /**
     * @inheritdoc
     */
    protected function doGetValues()
    {
        return (array)(new Parser())->parse($this->getFileContents());
    }

    /**
     * @inheritdoc
     */
    protected function doFlush()
    {
        $dumper = new Dumper();
        $this->setFileContents($dumper->dump($this->getValues(), 20));

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getFileName()
    {
        return 'bluesteel42_settings.yml';
    }
}