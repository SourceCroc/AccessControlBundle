<?php

namespace SourceCroc\AccessControlBundle\Controller;

use SourceCroc\AccessControlBundle\Security\Jwt;
use SourceCroc\AccessControlBundle\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenController extends AbstractController
{
    #[Route(path: '/token/obtain', name: 'sourcecroc.token.receive', methods: ['POST'])]
    public function login(UserInterface $user, JwtService $jwt): Response
    {
        $payload = [
            'roles' => $user->getRoles(),
        ];

        $token = $jwt->create($user->getUserIdentifier(), $payload);
        return new JsonResponse([
            'token' => $token->toString(),
            'refresh' => $jwt->createRefresh($token)->toString(),
        ]);
    }

    #[Route(path: '/token/refresh', name: 'sourcecroc.token.refresh', methods: ['POST'])]
    public function refresh(UserInterface $user, JwtService $jwt): Response
    {
        $payload = [
            'roles' => $user->getRoles(),
        ];
        $token = $jwt->create($user->getUserIdentifier(), $payload);

        return new JsonResponse([
            'token' => $token,
            'refresh' => $jwt->createRefresh($token)->toString(),
        ]);
    }
}