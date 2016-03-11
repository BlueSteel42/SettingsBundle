<?php

namespace BlueSteel42\SettingsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;


class BlueSteel42SettingsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $backend = key($config['backend']);

        switch($backend) {
            case 'xml':
            case 'yml':
                $container->setParameter('bluesteel42.settings.' . $backend . '.path', $config['backend'][$backend]['path']);
                break;
        }

        $unused_backends = array_diff(array('yml', 'xml', 'doctrinedbal'), array($backend));

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->getDefinition('bluesteel42.settings')->replaceArgument(0, new Reference('bluesteel42.settings.adapter_'.$backend));

        foreach ($unused_backends as $b) {
            $container->removeDefinition('bluesteel42.settings.adapter_'.$b);
        }
    }
    public function getAlias()
    {
        return 'bluesteel42_settings';
    }

}
