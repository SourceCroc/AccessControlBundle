<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\DependencyInjection;

use SourceCroc\AccessControlBundle\AccessControl;
use SourceCroc\AccessControlBundle\Helper\TimeHelper;
use SourceCroc\AccessControlBundle\Provider\PermissionProviderInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder(AccessControl::ALIAS);
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
                                ->ifString()->then(TimeHelper::writtenIntervalToSeconds(...))
                            ->end()
                            ->defaultValue(3600)
                        ->end()
                        ->scalarNode('refresh_token_ttl')
                            ->beforeNormalization()
                                ->ifString()->then(TimeHelper::writtenIntervalToSeconds(...))
                            ->end()
                            ->defaultValue(3600 * 8 * 14)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('permissions')
                    ->children()
                        ->scalarNode('provider')
                            ->info('Pass a service implementing '.PermissionProviderInterface::class)
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
