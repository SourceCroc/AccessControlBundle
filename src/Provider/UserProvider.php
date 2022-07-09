<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SourceCroc\AccessControlBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private EntityManagerInterface $em;
    private EntityRepository $userRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->userRepository = $em->getRepository(User::Class);
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf(
                    '[SourceCroc][AccessControl] Invalid user class "%s", we only support "%s".',
                    get_class($user),
                    User::class
                )
            );
        }

        /** @var User $user */
        $user->setSecret($newHashedPassword);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf(
                    '[SourceCroc][AccessControl] Invalid user class "%s", we only support "%s".',
                    get_class($user),
                    User::class
                )
            );
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['username' => $identifier]);
        if ($user === null) {
            throw new UserNotFoundException();
        }
        return $user;
    }
}
