<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\DependencyInjection;

use SourceCroc\AccessControlBundle\AccessControl;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class AccessControlExtension extends Extension
{
    public function getAlias(): string
    {
        return AccessControl::ALIAS;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(AccessControl::SRCROOT.'/Resources/config'));
        $loader->load('services.yaml');
        $loader->load('services/factories.yaml');
        $loader->load('services/providers.yaml');
        $loader->load('services/repositories.yaml');
        $loader->load('services/voters.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $userConfig = $this->processConfiguration($configuration, $configs);

        $permissions = $userConfig['permissions']['provider'];
        $container->setAlias('sourcecroc.access-control.permission-provider', new Reference(substr($permissions, 1)));

        $accessControlReference = $container->getDefinition(AccessControl::class);
        $accessControlReference->setArgument('$authTokenTTL', $userConfig['authentication']['access_token_ttl']);
        $accessControlReference->setArgument('$refreshTokenTTL', $userConfig['authentication']['refresh_token_ttl']);
    }
}
