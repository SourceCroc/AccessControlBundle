<?php

namespace SourceCroc\AccessControlBundle;

final class AccessControlConstants
{
    const Alias = 'sourcecroc_access-control';
    const SrcRoot = __DIR__;

    const USER_TABLE = '`sourcecroc/access-control/users`';
    const ROLE_TABLE = '`sourcecroc/access-control/roles`';
    const PERMISSION_TABLE = '`sourcecroc/access-control/permissions`';

    const USER_ROLE_TABLE = '`sourcecroc/access-control/users_roles`';
    const ROLE_PERMISSION_TABLE = '`sourcecroc/access-control/roles_permissions`';
    const USER_PERMISSION_TABLE = '`sourcecroc/access-control/users_permissions`';

    private function __construct()
    { /* Prevent making instances of this class */
    }
}