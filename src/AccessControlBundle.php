<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle;

use SourceCroc\AccessControlBundle\DependencyInjection\AccessControlExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccessControlBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new AccessControlExtension();
        }

        return $this->extension;
    }

    public function build(ContainerBuilder $container)
    {
    }
}
