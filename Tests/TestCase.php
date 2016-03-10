<?php

namespace BlueSteel42\SettingsBundle\Tests;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use BlueSteel42\SettingsBundle\DependencyInjection\BlueSteel42SettingsExtension;
use BlueSteel42\SettingsBundle\BlueSteel42SettingsBundle;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function getRawContainer()
    {
        $container = new ContainerBuilder();
        $settings = new BlueSteel42SettingsExtension();
        $container->registerExtension($settings);

        $bundle = new BlueSteel42SettingsBundle();
        $bundle->build($container);

        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());

        return $container;
    }

    protected function getContainer()
    {
        $container = $this->getRawContainer();
        $container->compile();

        return $container;
    }
}
