<?php

namespace SourceCroc\AccessControlBundle\Service;

use http\Exception\InvalidArgumentException;
use SourceCroc\AccessControlBundle\Factory\JwtFactory;
use SourceCroc\AccessControlBundle\Security\Jwt;
use SourceCroc\AccessControlBundle\Security\Token\JwtSigner;
use SourceCroc\AccessControlBundle\Security\Token\RefreshHeader;

class JwtService
{
    private JwtFactory $jwtFactory;
    private JwtSigner $signer;

    public function __construct(JwtFactory $jwtFactory, JwtSigner $signer)
    {
        $this->jwtFactory = $jwtFactory;
        $this->signer = $signer;
    }

    public function validate(Jwt $jwt, ?Jwt $refreshToken = null): bool
    {
        $jwtHeader = $jwt->getHeader()->toString();
        $jwtPayload = $jwt->getPayload()->toString();

        $valid = true;
        if ($refreshToken === null) {
            $valid &= $jwt->stillValid();
        } else {
            /** @var RefreshHeader $header */
            $header = $refreshToken->getHeader();
            $valid &= $header->validFor($jwt->getSignature());
            $valid &= $refreshToken->stillValid();
            $valid &= $refreshToken->getSignature() === $this->signer->sign($header->toString());
        }
        $valid &= $jwt->getSignature() === $this->signer->sign("$jwtHeader.$jwtPayload");
        return $valid;
    }

    public function create(string $userIdentifier, array $payload): Jwt
    {
        $datetime = new \DateTimeImmutable('now + 5 seconds', new \DateTimeZone('UTC'));
        return $this->jwtFactory->create($datetime, $userIdentifier, $payload);
    }

    public function createRefresh(Jwt $jwt): ?Jwt
    {
        if ($jwt->getHeader()->getType() === 'refresh') {
            throw new InvalidArgumentException('refresh tokens cannot be refreshed');
        }

        $userIdentifier = $jwt->getHeader()->getUserIdentifier();
        $datetime = new \DateTimeImmutable('now + 10 seconds', new \DateTimeZone('UTC'));

        $token = $this->jwtFactory->create($datetime, $userIdentifier, null, 'refresh');
        /** @var RefreshHeader $header */
        $header = $token->getHeader();
        $header->setForSignature($jwt->getSignature());
        return $token;
    }
}
