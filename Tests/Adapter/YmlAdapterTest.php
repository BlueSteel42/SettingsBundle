<?php

namespace BlueSteel42\SettingsBundle\Tests\Adapter;

use BlueSteel42\SettingsBundle\Adapter\AdapterInterface;
use BlueSteel42\SettingsBundle\Tests\app\AppKernel;

class YmlAdapterTest extends BaseAdapterTester
{
    protected $env = 'yml';
}