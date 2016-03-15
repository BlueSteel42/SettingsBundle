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
     * @return array
     */
    protected function doGetValues()
    {
        return (array)(new Parser())->parse($this->getFileContents());
    }

    public function flush()
    {
        $yaml = (new Dumper())->dump($this->getValues(), 120);
        $this->setFileContents($yaml);

        return $this;
    }

    protected function getFileName()
    {
        return 'bluesteel42_settings.yml';
    }
}