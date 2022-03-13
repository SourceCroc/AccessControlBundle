<?php

namespace SourceCroc\AccessControlBundle;

final class AccessControl
{
    const Alias = 'sourcecroc_access-control';
    const SrcRoot = __DIR__;

    const USER_TABLE = '`sourcecroc/access-control/users`';
    const ROLE_TABLE = '`sourcecroc/access-control/roles`';
    const PERMISSION_TABLE = '`sourcecroc/access-control/permissions`';

    const USER_ROLE_TABLE = '`sourcecroc/access-control/users_roles`';
    const ROLE_PERMISSION_TABLE = '`sourcecroc/access-control/roles_permissions`';
    const USER_PERMISSION_TABLE = '`sourcecroc/access-control/users_permissions`';

    private int $authTokenTTL;
    private int $refreshTokenTTL;

    public function __construct(int $authTokenTTL, int $refreshTokenTTL)
    {
        $this->authTokenTTL = $authTokenTTL;
        $this->refreshTokenTTL = $refreshTokenTTL;
    }

    /**
     * @return int TTL in seconds
     */
    public function getAuthTokenTTL(): int
    {
        return $this->authTokenTTL;
    }

    /**
     * @return int TTL in seconds
     */
    public function getRefreshTokenTTL(): int
    {
        return $this->refreshTokenTTL;
    }
}