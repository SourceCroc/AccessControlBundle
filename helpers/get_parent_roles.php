<?php declare(strict_types=1);

namespace SourceCroc\Helpers;

use SourceCroc\AccessControlBundle\Entity\Role;

if (!function_exists(__NAMESPACE__.'\\sourcecroc_get_parent_roles')) {
    /**
     * @param Role[] $roles
     * @return Role[]
     */
    function sourcecroc_get_parent_roles(array $roles): array
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
