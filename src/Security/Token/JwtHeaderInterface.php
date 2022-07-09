<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Security\Token;

interface JwtHeaderInterface
{
    public function getType(): string;
    public function getExpiresOn(): \DateTimeImmutable;
    public function getUserIdentifier(): string;

    public function toString(): string;
    public function __toString(): string;

    public static function fromString(string $encoded): self;
}
