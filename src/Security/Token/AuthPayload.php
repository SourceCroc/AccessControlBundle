<?php

namespace SourceCroc\AccessControlBundle\Security\Token;

use SourceCroc\AccessControlBundle\Security\Jwt;

class AuthPayload implements JwtPayloadInterface
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function toString(): string
    {
        return Jwt::base64UrlEncode($this->data);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function fromString(string $encodedPayload): AuthPayload
    {
        $payload = Jwt::base64UrlDecode($encodedPayload);
        return new AuthPayload($payload);
    }

    public function get(string $identifier, mixed $default = null): mixed
    {
        return array_key_exists($identifier, $this->data) ? $this->data[$identifier] : $default;
    }
}
