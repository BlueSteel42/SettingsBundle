<?php

namespace BlueSteel42\SettingsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class BlueSteel42SettingsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $cache_service = $this->setCache($config, $container);
        $this->setBackend($config, $container, $cache_service);

        $container->setParameter('bluesteel42.setting.exceptions', $config['exceptions']);
    }

    public function getAlias()
    {
        return 'bluesteel42_settings';
    }

    protected function setBackend(array $config, ContainerBuilder $container, $cache_service)
    {

        $backend = key($config['backend']);
        $unused_backends = array_diff(array('yml', 'xml', 'doctrinedbal'), array($backend));

        switch ($backend) {
            case 'xml':
            case 'yml':
                $container->setParameter('bluesteel42.settings.' . $backend . '.path', $config['backend'][$backend]['path']);
                break;
            case 'doctrinedbal':
                $container->setParameter('bluesteel42.settings.' . $backend . '.table', $config['backend'][$backend]['table']);
                $container->setParameter('bluesteel42.settings.' . $backend . '.connection', $config['backend'][$backend]['connection']);
                break;
        }

        $container->getDefinition('bluesteel42.settings.adapter_'.$backend)->addMethodCall('setCacheManager', array(new Reference($cache_service)));
        $container->getDefinition('bluesteel42.settings')->replaceArgument(0, new Reference('bluesteel42.settings.adapter_'.$backend));

        foreach ($unused_backends as $b) {
            $container->removeDefinition('bluesteel42.settings.adapter_' . $b);
        }
    }

    protected function setCache(array $config, ContainerBuilder $container)
    {

        $cache = key($config['cache']);
        $unused_caches = array_diff(array('null', 'file', 'memcached'), array($cache));

        switch ($cache) {
            case 'null':
                break;
            case 'file':
                $container->setParameter('bluesteel42.settings.' . $cache . '.path', $config['cache'][$cache]['path']);
                break;
            case 'memcached':
                $def = $container->getDefinition('bluesteel42.settings.cache_memcached');
                foreach($config['cache']['memcached']['servers'] as $s){
                    $def->addMethodCall('addServer', array($s['host'], $s['port']));
                }
                break;
        }

        foreach ($unused_caches as $b) {
            $container->removeDefinition('bluesteel42.settings.cache_' . $b);
        }

        return 'bluesteel42.settings.cache_' . $cache;
    }

}
