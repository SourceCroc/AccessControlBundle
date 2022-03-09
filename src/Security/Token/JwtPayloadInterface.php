<?php

namespace SourceCroc\AccessControlBundle\Security\Token;

interface JwtPayloadInterface
{
    public function __construct(array $data);
    public function __toString(): string;
    public function toString(): string;

    public function get(string $identifier, mixed $default = null): mixed;
}
