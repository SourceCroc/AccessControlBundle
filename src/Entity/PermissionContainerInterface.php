<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Entity;

interface PermissionContainerInterface
{
    public function hasPermission(PermissionInterface|string $permission): bool;

    public function addPermission(PermissionInterface $permission): self;

    public function removePermission(PermissionInterface $permission): self;
}
