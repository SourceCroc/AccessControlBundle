<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Provider;

interface PermissionProviderInterface
{
    public function getAllPermissions(): array;
}
