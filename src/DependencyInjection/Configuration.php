<?php

namespace SourceCroc\AccessControlBundle\DependencyInjection;

require_once __DIR__.'/../../helpers/interval_to_seconds.php';

use SourceCroc\AccessControlBundle\AccessControl;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use function SourceCroc\Helpers\sourcecroc_interval_to_seconds;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder(AccessControl::Alias);
        $root = $builder->getRootNode();

        $this->addPermissionSection($root);

        return $builder;
    }

    public function addPermissionSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('authentication')
                    ->children()
                        ->scalarNode('access_token_ttl')
                            ->beforeNormalization()
                                ->ifString()->then(sourcecroc_interval_to_seconds(...))
                            ->end()
                            ->defaultValue(3600)
                        ->end()
                        ->scalarNode('refresh_token_ttl')
                            ->beforeNormalization()
                                ->ifString()->then(sourcecroc_interval_to_seconds(...))
                            ->end()
                            ->defaultValue(3600 * 8 * 14)
                        ->end()
                    ->end()
                ->end()
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