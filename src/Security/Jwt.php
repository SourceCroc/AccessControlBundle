<?php

namespace SourceCroc\AccessControlBundle\Security;

use SourceCroc\AccessControlBundle\Security\Token\AuthHeader;
use SourceCroc\AccessControlBundle\Security\Token\JwtHeaderInterface;
use SourceCroc\AccessControlBundle\Security\Token\JwtPayloadInterface;

class Jwt
{
    private JwtHeaderInterface $header;

    private ?JwtPayloadInterface $payload;

    private string $signature;

    public function __construct(JwtHeaderInterface $header, ?JwtPayloadInterface $payload, string $signature)
    {
        $this->header = $header;
        $this->payload = $payload;
        $this->signature = $signature;
    }

    public function getHeader(): JwtHeaderInterface
    {
        return $this->header;
    }

    public function getPayload(): ?JwtPayloadInterface
    {
        return $this->payload;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function stillValid(): bool
    {
        $expiresOn = $this->getHeader()->getExpiresOn();
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        return $expiresOn > $now;
    }

    public function toString(): string
    {
        $parts = [];
        $parts[] = $this->getHeader()->toString();
        $parts[] = $this->getPayload()?->toString();
        $parts[] = self::getSignature();
        return implode('.', array_filter($parts));
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function base64UrlEncode(string|array $subject): string
    {
        if (is_array($subject)) {
            $subject = json_encode($subject);
        }
        return rtrim(strtr(base64_encode($subject), '+/', '-_'), '=');
    }

    public static function base64UrlDecode(string $encoded, bool $returnArray = true): string|array
    {
        $result = base64_decode(strtr($encoded, '-_', '+/'), true);
        if ($returnArray) {
            return json_decode($result, true);
        }
        return $result;
    }
}