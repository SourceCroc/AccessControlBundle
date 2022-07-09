<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Factory;

use SourceCroc\AccessControlBundle\Security\Jwt;
use SourceCroc\AccessControlBundle\Security\Token\AuthHeader;
use SourceCroc\AccessControlBundle\Security\Token\AuthPayload;
use SourceCroc\AccessControlBundle\Security\Token\JwtHeaderInterface;
use SourceCroc\AccessControlBundle\Security\Token\JwtPayloadInterface;
use SourceCroc\AccessControlBundle\Security\Token\JwtSigner;
use SourceCroc\AccessControlBundle\Security\Token\RefreshHeader;

class JwtFactory
{
    private JwtSigner $signer;

    public function __construct(JwtSigner $signer)
    {
        $this->signer = $signer;
    }

    /**
     * @throws \Exception
     */
    public function create(
        int $ttl,
        string $userIdentifier,
        ?array $payloadData = null,
        string $type = 'auth',
    ): Jwt {
        try {
            $eon = new \DateTimeImmutable("now + $ttl seconds", new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new \Exception('JwtFactory::create argument 1: $ttl is not a valid number');
        }

        /** @var JwtHeaderInterface $header */
        $header = new ($type === 'auth' ? AuthHeader::class : RefreshHeader::class)($eon, $userIdentifier);

        /** @var ?JwtPayloadInterface $payload */
        $payload = is_array($payloadData) ? new AuthPayload($payloadData) : $payloadData;

        $stringHeader = $header->toString();
        $stringPayload = $payload !== null ? $payload : null;
        $signee = implode('.', array_filter([$stringHeader, $stringPayload]));
        return new Jwt($header, $payload, $this->signer->sign("$signee"));
    }
}
