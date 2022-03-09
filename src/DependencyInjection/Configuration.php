<?php

namespace SourceCroc\AccessControlBundle\DependencyInjection;

use SourceCroc\AccessControlBundle\AccessControlConstants;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder(AccessControlConstants::Alias);
        $root = $builder->getRootNode();

        $this->addPermissionSection($root);

        return $builder;
    }

    public function addPermissionSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('permissions')
                    ->children()
                        ->scalarNode('provider')
                            ->info('Pass a service implementing SourceCroc\\AccessControlBundle\\Provider\\PermissionProviderInterface')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}