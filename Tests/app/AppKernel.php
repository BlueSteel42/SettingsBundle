<?php

namespace BlueSteel42\SettingsBundle\Tests\app;

use BlueSteel42\SettingsBundle\BlueSteel42SettingsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{

    /**
     * @inheritdoc
     */
    public function registerBundles()
    {
        return array(
            new FrameworkBundle(),
            new BlueSteel42SettingsBundle()
        );
    }

    /**
     * @inheritdoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config_'.$this->getEnvironment().'.yml');
    }
}