<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Security\Token;

use SourceCroc\AccessControlBundle\Security\Jwt;

class JwtSigner
{
    private string $jwtSecret;

    public function __construct(string $jwtSecret)
    {
        $this->jwtSecret = $jwtSecret;
    }

    public function sign(string $signee): string
    {
        return Jwt::base64UrlEncode(hash_hmac('sha512', $signee, $this->jwtSecret, true));
    }
}
