<?php

namespace BlueSteel42\SettingsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bluesteel42_settings');

        $defaultPath = '%kernel.root_dir%/Resources';

        $rootNode
            ->beforeNormalization()
                ->always(function($v) {
                    if (!is_array($v) || !isset($v['backend'])) {
                        return array('backend' => null);
                    }
                    return $v;
                })
            ->end()
            ->children()
                ->arrayNode('backend')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function($v) { return array($v => null ); })
                    ->end()
                    ->treatNullLike(array('yml' => null))
                    ->children()
                        ->arrayNode('doctrinedbal')
                            ->treatNullLike(array('connection' => 'default', 'table' => 'bluesteel42_settings'))
                            ->children()
                                ->scalarNode('connection')
                                    ->defaultValue('default')
                                    ->treatNullLike('default')
                                ->end()
                                ->scalarNode('table')
                                    ->defaultValue('bluesteel42_settings')
                                    ->treatNullLike('bluesteel42_settings')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('yml')
                            ->treatNullLike(array('path' => $defaultPath))
                            ->children()
                                ->scalarNode('path')
                                    ->defaultValue($defaultPath)
                                    ->treatNullLike($defaultPath)
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('xml')
                            ->treatNullLike(array('path' => $defaultPath))
                            ->children()
                                ->scalarNode('path')
                                    ->defaultValue($defaultPath)
                                    ->treatNullLike($defaultPath)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function($v) {
                            return count($v) > 1;
                        })
                        ->thenInvalid('You must configure only one backend among "yml", "xml" and "doctrinedbal".')
                    ->end()
                ->end()
                ->booleanNode('exceptions')->defaultValue(false)->end()
                ->arrayNode('cache')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function($v) { return array($v => null ); })
                    ->end()
                    ->treatNullLike(array('null' => null))
                    ->children()
                        ->booleanNode('null')
                            ->treatNullLike(true)
                        ->end()
                        ->arrayNode('file')
                            ->treatNullLike(array('path' => '%kernel.cache_dir%'.DIRECTORY_SEPARATOR.'bluesteel42_settings'))
                            ->children()
                                ->scalarNode('path')
                                    ->defaultValue('%kernel.cache_dir%')
                                    ->treatNullLike('%kernel.cache_dir%')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('memcached')
                            ->children()
                                ->arrayNode('servers')
                                    ->isRequired()
                                    ->requiresAtLeastOneElement()
                                    ->prototype('array')
                                    ->children()
                                        ->scalarNode('host')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('port')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->validate()
                    ->ifTrue(function($v) {
                        return count($v) > 1;
                    })
                    ->thenInvalid('You must configure only one cache among "null", "file" and "memcached".')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
