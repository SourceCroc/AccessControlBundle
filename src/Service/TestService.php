<?php

namespace SourceCroc\AccessControlBundle\Service;

use SourceCroc\AccessControlBundle\Provider\PermissionProviderInterface;

class TestService
{
    private PermissionProviderInterface $blaat;

    public function __construct(PermissionProviderInterface $blaat)
    {
        $this->blaat = $blaat;
    }

    public function getPermissions(): array
    {
        return $this->blaat->getAllPermissions();
    }
}