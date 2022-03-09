<?php

namespace SourceCroc\AccessControlBundle\DependencyInjection;

use SourceCroc\AccessControlBundle\AccessControlConstants;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\File\File;

class AccessControlExtension extends Extension
{
    public function getAlias(): string
    {
        return AccessControlConstants::Alias;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $userConfig = $this->processConfiguration($configuration, $configs);

        $permissions = $userConfig['permissions']['provider'];
        $container->setAlias('sourcecroc.access-control.perm-provider', new Reference(substr($permissions, 1)));
    }
}
