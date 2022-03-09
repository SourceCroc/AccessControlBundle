<?php

namespace SourceCroc\AccessControlBundle\Entity;

interface PermissionInterface
{
    #[Pure] public function getIdentifier(): string;

    #[Pure] public function __toString(): string;
}