<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use SourceCroc\AccessControlBundle\Entity\UsedToken;
use SourceCroc\AccessControlBundle\Repository\UsedTokenRepository;
use SourceCroc\AccessControlBundle\Security\Jwt;
use SourceCroc\AccessControlBundle\Security\Token\AuthHeader;
use SourceCroc\AccessControlBundle\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function refresh(
        Request $request,
        UserInterface $user,
        JwtService $jwt,
        EntityManagerInterface $em,
    ): Response {
        $authToken = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        $refreshToken = $request->headers->get('X-Refresh-Token');

        $usedToken = new UsedToken();
        $usedToken->setToken($authToken);
        $usedToken->setRefreshToken($refreshToken);

        $header = explode('.', $refreshToken)[0];
        $usedToken->setExpiresOn(AuthHeader::fromString($header)->getExpiresOn());

        $em->persist($usedToken);
        $em->flush();

        $payload = [
            'roles' => $user->getRoles(),
        ];
        $token = $jwt->create($user->getUserIdentifier(), $payload);

        return new JsonResponse([
            'token' => $token->toString(),
            'refresh' => $jwt->createRefresh($token)->toString(),
        ]);
    }

    #[Route(path: '/token/check', name: 'sourcecroc.token.check', methods: ['GET|HEAD'])]
    public function check(): Response
    {
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
