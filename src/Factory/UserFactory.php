<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Factory;

use Doctrine\ORM\EntityManagerInterface;
use SourceCroc\AccessControlBundle\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
    ) {
        $this->em = $em;
        $this->hasher = $hasher;
    }

    public function createUser(string $username, string $secret): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setSecret($this->hashData($user, $secret));
        $this->em->persist($user);
        return $user;
    }

    public function hashData(User $user, string $secret): string
    {
        return $this->hasher->hashPassword($user, $secret);
    }
}
