<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle;

final class AccessControl
{
    public const ALIAS = 'sourcecroc_access-control';
    public const SRCROOT = __DIR__;

    public const USER_TABLE = '`sourcecroc/access-control/users`';
    public const ROLE_TABLE = '`sourcecroc/access-control/roles`';
    public const PERMISSION_TABLE = '`sourcecroc/access-control/permissions`';

    public const USER_ROLE_TABLE = '`sourcecroc/access-control/users_roles`';
    public const ROLE_PERMISSION_TABLE = '`sourcecroc/access-control/roles_permissions`';
    public const USER_PERMISSION_TABLE = '`sourcecroc/access-control/users_permissions`';

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
