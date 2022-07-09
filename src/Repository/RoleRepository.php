<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use SourceCroc\AccessControlBundle\Entity\Role;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;

/**
 * @template-extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function save(Role $role): void
    {
        $this->getEntityManager()->persist($role);

        try {
            $this->getEntityManager()->flush();
        } catch (DuplicateKeyException $exception) {
            // @TODO: Throw custom exception
            throw $exception;
        }
    }

    public function remove(Role|int|string $role): bool
    {
        if (is_string($role)) {
            $role = $this->findOneBy(['identifier' => $role]);
        } elseif (is_int($role)) {
            $role = $this->findOneBy(['id' => $role]);
        }

        $this->getEntityManager()->remove($role);

        try {
            $this->getEntityManager()->flush();
        } catch (ForeignKeyConstraintViolationException) {
            return false;
        }

        return true;
    }

    public function list(): array
    {
        return $this->createQueryBuilder('r')->getQuery()->getResult();
    }
}
