<?php

namespace BlueSteel42\SettingsBundle\Tests;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use BlueSteel42\SettingsBundle\Tests\app\AppKernel;

use BlueSteel42\SettingsBundle\DependencyInjection\BlueSteel42SettingsExtension;
use BlueSteel42\SettingsBundle\BlueSteel42SettingsBundle;
use Symfony\Component\HttpKernel\KernelInterface;

class TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var KernelInterface[]
     */
    protected $kernels = array('yml' => null, 'xml' => null, 'doctrinedbal' => null);

    /**
     * @return ContainerBuilder
     */
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

    /**
     * @param string $environment
     * @param bool $debug
     * @return KernelInterface
     */
    protected function getKernel($environment, $debug = true)
    {
        if (null == $this->kernels[$environment]) {
            $this->kernels[$environment] = new AppKernel($environment, $debug);
        }

        return $this->kernels[$environment];
    }
}
