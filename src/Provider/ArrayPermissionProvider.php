<?php

namespace SourceCroc\AccessControlBundle\Provider;

use JetBrains\PhpStorm\ArrayShape;

class ArrayPermissionProvider implements PermissionProviderInterface
{
    /**
     * @var string[]
     */
    private array $permissions;

    public function __construct(array $permissions = [])
    {
        $this->permissions = $permissions;
    }

    public function getAllPermissions(): array
    {
        return $this->permissions;
    }
}