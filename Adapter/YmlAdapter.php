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
        return (new Parser())->parse($this->getFileContents());
    }

    public function flush()
    {
        // TODO: Implement flush() method.
    }

    protected function getFileName()
    {
        return 'bluesteel42_settings.yml';
    }
}