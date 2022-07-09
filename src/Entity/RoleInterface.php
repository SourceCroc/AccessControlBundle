<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Entity;

interface RoleInterface extends PermissionContainerInterface
{
    public function getIdentifier(): string;

    public function __toString(): string;
}
