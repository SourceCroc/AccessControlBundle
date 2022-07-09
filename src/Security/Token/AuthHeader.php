<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Security\Token;

use SourceCroc\AccessControlBundle\Security\Jwt;

class AuthHeader implements JwtHeaderInterface
{
    protected \DateTimeImmutable $eon;

    protected string $userIdentifier;

    public function __construct(\DateTimeImmutable $eon, string $userIdentifier)
    {
        $this->eon = $eon;
        $this->userIdentifier = $userIdentifier;
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

    public static function fromString(string $encodedHeader): AuthHeader
    {
        $headerArray = Jwt::base64UrlDecode($encodedHeader);

        $username = $headerArray['userIdentifier'];
        $datetime = new \DateTimeImmutable($headerArray['eon'], new \DateTimeZone('UTC'));

        return new AuthHeader($datetime, $username);
    }

    public function getType(): string
    {
        return 'auth';
    }

    public function getExpiresOn(): \DateTimeImmutable
    {
        return $this->eon;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }
}
