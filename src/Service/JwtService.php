<?php

namespace SourceCroc\AccessControlBundle\Service;

use http\Exception\InvalidArgumentException;
use SourceCroc\AccessControlBundle\AccessControl;
use SourceCroc\AccessControlBundle\Factory\JwtFactory;
use SourceCroc\AccessControlBundle\Security\Jwt;
use SourceCroc\AccessControlBundle\Security\Token\JwtSigner;
use SourceCroc\AccessControlBundle\Security\Token\RefreshHeader;

class JwtService
{
    private JwtFactory $jwtFactory;
    private JwtSigner $signer;
    private AccessControl $constants;

    public function __construct(JwtFactory $jwtFactory, JwtSigner $signer, AccessControl $constants)
    {
        $this->jwtFactory = $jwtFactory;
        $this->signer = $signer;
        $this->constants = $constants;
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

    /**
     * @throws \Exception gets thrown when the configured ttl is invalid
     */
    public function create(string $userIdentifier, array $payload): Jwt
    {
        return $this->jwtFactory->create($this->constants->getAuthTokenTTL(), $userIdentifier, $payload);
    }

    /**
     * @throws \Exception gets thrown when the configured ttl is invalid
     */
    public function createRefresh(Jwt $jwt): ?Jwt
    {
        if ($jwt->getHeader()->getType() === 'refresh') {
            throw new InvalidArgumentException('refresh tokens cannot be refreshed');
        }

        $userIdentifier = $jwt->getHeader()->getUserIdentifier();
        $token = $this->jwtFactory->create(
            $this->constants->getRefreshTokenTTL(),
            $userIdentifier,
            null,
            'refresh'
        );

        /** @var RefreshHeader $header */
        $header = $token->getHeader();
        $header->setForSignature($jwt->getSignature());
        return $token;
    }
}
