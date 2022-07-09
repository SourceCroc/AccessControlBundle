<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Provider;

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
