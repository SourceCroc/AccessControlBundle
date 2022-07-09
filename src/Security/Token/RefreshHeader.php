<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Security\Token;

use SourceCroc\AccessControlBundle\Security\Jwt;

class RefreshHeader implements JwtHeaderInterface
{
    private \DateTimeImmutable $eon;

    private string $userIdentifier;

    private ?string $forSignature = null;

    public function __construct(\DateTimeImmutable $eon, string $userIdentifier)
    {
        $this->eon = $eon;
        $this->userIdentifier = $userIdentifier;
    }

    public function setForSignature(string $signature): void
    {
        $this->forSignature = $signature;
    }

    public function validFor(string $signature): bool
    {
        return $this->forSignature === $signature;
    }

    public function getType(): string
    {
        return 'refresh';
    }

    public function getExpiresOn(): \DateTimeImmutable
    {
        return $this->eon;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return Jwt::base64UrlEncode([
            'type' => $this->getType(),
            'eon' => $this->getExpiresOn()->format(DATE_ISO8601),
            'userIdentifier' => $this->getUserIdentifier(),
        ]);
    }

    public static function fromString(string $encoded): RefreshHeader
    {
        $headerArray = Jwt::base64UrlDecode($encoded);

        $username = $headerArray['userIdentifier'];
        $datetime = new \DateTimeImmutable($headerArray['eon'], new \DateTimeZone('UTC'));

        return new RefreshHeader($datetime, $username);
    }
}
