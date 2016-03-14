<?php

namespace BlueSteel42\SettingsBundle\Tests\DependencyInjection;

use BlueSteel42\SettingsBundle\Service\Settings;
use BlueSteel42\SettingsBundle\Tests\TestCase;

class DependencyInjectionTest extends TestCase
{

    public function testExtension()
    {
        $container = $this->getRawContainer();

        $this->assertTrue($container->hasExtension('bluesteel42_settings'));
    }

    public function testService()
    {
        $ymlKernel = $this->getKernel('yml');
        $this->assertTrue($ymlKernel->getContainer()->get('bluesteel42.settings') instanceof Settings);
    }

}