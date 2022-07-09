<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use SourceCroc\AccessControlBundle\Entity\Permission;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;

/**
 * @template-extends ServiceEntityRepository<Permission>
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function save(Permission $permission): void
    {
        $this->getEntityManager()->persist($permission);

        try {
            $this->getEntityManager()->flush();
        } catch (DuplicateKeyException $exception) {
            // @TODO: Throw custom exception
            throw $exception;
        }
    }

    public function remove(Permission $permission): bool
    {
        $this->getEntityManager()->remove($permission);

        try {
            $this->getEntityManager()->flush();
        } catch (ForeignKeyConstraintViolationException) {
            return false;
        }

        return true;
    }

    public function list(): array
    {
        return $this->createQueryBuilder('p')->getQuery()->getResult();
    }
}
