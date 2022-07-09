<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Helper;

use JetBrains\PhpStorm\Pure;
use SourceCroc\AccessControlBundle\Entity\Role;

abstract class RoleHelper
{
    /**
     * @param Role[] $roles
     * @return Role[]
     */
    #[Pure]
    public static function resolveParentRoles(array $roles): array
    {
        $parents = [];
        $higherLevelFound = false;
        do {
            $found = array_unique(array_filter(array_map(fn(Role $role) => $role->getInheritsFrom(), $roles)));
            $currentParentCount = count($parents);
            array_push($parents, ...$found);
            $parents = array_unique($parents);
            $higherLevelFound = count($parents) !== $currentParentCount;
        } while ($higherLevelFound);

        return $parents;
    }
}
