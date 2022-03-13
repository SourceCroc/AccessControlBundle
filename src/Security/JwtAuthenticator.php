<?php

declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Security;

use JetBrains\PhpStorm\ArrayShape;
use SourceCroc\AccessControlBundle\Security\Token\AuthHeader;
use SourceCroc\AccessControlBundle\Security\Token\AuthPayload;
use SourceCroc\AccessControlBundle\Security\Token\RefreshHeader;
use SourceCroc\AccessControlBundle\Service\JwtService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class JwtAuthenticator extends AbstractAuthenticator
{
    private string $refreshRoute;
    private JwtService $jwtService;

    public function __construct(string $refreshRoute, JwtService $jwtService)
    {
        $this->refreshRoute = $refreshRoute;
        $this->jwtService = $jwtService;
    }

    public function supports(Request $request): bool
    {
        $tokenAndType = $this->resolveTokenAndType($request);

        $supportableConstraints = true;
        $supportableConstraints &= $tokenAndType['token'] !== null;

        if ($tokenAndType['token'] !== null) {
            $supportableConstraints &= (substr_count($tokenAndType['token'], '.') === 2);
        }

        if ($tokenAndType['type'] === 'refresh') {
            $supportableConstraints &= $request->isMethod(Request::METHOD_POST);
            $supportableConstraints &= $request->get('_route') === $this->refreshRoute;
        }

        return $supportableConstraints == true;
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        $callback = fn(Jwt $token) => $this->jwtService->validate($token);
        if ($credentials['refresh']) {
            $credentials['secret'] = [
                'auth' => $credentials['secret'],
                'refresh' => $credentials['refresh'],
            ];
            $callback = fn(array $tokens) => $this->jwtService->validate($tokens['auth'], $tokens['refresh']);
        }

        return new Passport(
            new UserBadge($credentials['username']),
            new CustomCredentials(
                $callback,
                $credentials['secret'],
            ),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => ['ERROR.AUTHENTICATION.INVALID_CREDENTIALS']], Response::HTTP_UNAUTHORIZED);
    }

    #[ArrayShape(['username' => 'string', 'secret' => 'string', 'refresh' => '?string'])]
    private function getCredentials(Request $request): ?array
    {
        $tokenAndType = $this->resolveTokenAndType($request);
        list($header, $payload, $signature) = explode('.', $tokenAndType['token']);
        $jwtHeader = AuthHeader::fromString($header);
        $jwtPayload = AuthPayload::fromString($payload);
        $jwt = new Jwt($jwtHeader, $jwtPayload, $signature);

        if ($tokenAndType['refresh'] !== null) {
            list($header, $refreshSignature) = explode('.', $tokenAndType['refresh']);
            $refreshHeader = RefreshHeader::fromString($header);
            $refreshHeader->setForSignature($signature);
            $tokenAndType['refresh'] = new Jwt($refreshHeader, null, $refreshSignature);
        }

        return [
            'username' => $jwt->getHeader()->getUserIdentifier(),
            'secret' => $jwt,
            'refresh' => $tokenAndType['refresh'],
        ];
    }

    #[ArrayShape(['token' => '?string', 'refresh' => '?string', 'type' => 'string'])]
    private function resolveTokenAndType(Request $request): array
    {
        $authToken = $request->headers->get('Authorization');
        $refreshToken = $request->headers->get('X-Refresh-Token');

        if ($authToken !== null) {
            $authToken = str_replace('Bearer', '', $authToken);
        }

        return [
            'token' => $authToken,
            'refresh' => $refreshToken,
            'type' => ($refreshToken === null) ? 'authenticate' : 'refresh',
        ];
    }
}