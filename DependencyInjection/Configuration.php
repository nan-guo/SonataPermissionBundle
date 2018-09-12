<?php

namespace Prodigious\Sonata\PermissionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('prodigious_sonata_permission');

        $rootNode
            ->children()
                ->arrayNode('default_roles')
                    ->scalarPrototype()->end()
                ->end()
                ->booleanNode('auto_replace_roles_field')
                    ->defaultTrue()
                ->end()
                ->arrayNode('groups')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('label')->end()
                            ->scalarNode('translation_domain')->defaultValue('ProdigiousSonataPermissionBundle')->end()
                            ->arrayNode('items')
                                ->requiresAtLeastOneElement()
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('type')->cannotBeEmpty()->end()
                                        ->scalarNode('name')->cannotBeEmpty()->end()
                                        ->scalarNode('label')->defaultNull()->end()
                                        ->scalarNode('translation_domain')->defaultValue('ProdigiousSonataPermissionBundle')->end()
                                        ->arrayNode('permissions')
                                            ->defaultValue(['Create' => 'CREATE', 'Edit' => 'EDIT', 'List' => 'LIST',  'View' => 'VIEW', 'Delete' => 'DELETE', 'Export' => 'EXPORT'])
                                            ->prototype('scalar')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
