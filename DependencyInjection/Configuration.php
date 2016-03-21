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
                            ->treatNullLike(array('connection' => 'default'))
                            ->children()
                                ->scalarNode('connection')
                                    ->defaultValue('default')
                                    ->treatNullLike('default')
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
            ->end();

        return $treeBuilder;
    }
}
