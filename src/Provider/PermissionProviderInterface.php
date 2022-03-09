<?php

namespace SourceCroc\AccessControlBundle\Provider;

interface PermissionProviderInterface
{
    public function getAllPermissions(): array;
}