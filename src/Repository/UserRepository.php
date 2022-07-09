<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use SourceCroc\AccessControlBundle\Entity\User;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;

/**
 * @template-extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);

        try {
            $this->getEntityManager()->flush();
        } catch (DuplicateKeyException $exception) {
            // @TODO: throw custom exception
            throw $exception;
        }
    }

    public function remove(User|int $user): bool
    {
        if (is_int($user)) {
            $user = $this->getEntityManager()->getReference(User::class, $user);
        }

        $this->getEntityManager()->remove($user);

        try {
            $this->getEntityManager()->flush();
        } catch (ForeignKeyConstraintViolationException) {
            return false;
        }

        return true;
    }

    public function list(): array
    {
        return $this->createQueryBuilder('u')->getQuery()->getResult();
    }
}
