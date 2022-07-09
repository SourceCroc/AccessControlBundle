<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use SourceCroc\AccessControlBundle\Entity\UsedToken;

class UsedTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsedToken::class);
    }

    /**
     * @param string $token
     * @param string|null $refresh
     * @return UsedToken|null
     * @throws NonUniqueResultException
     */
    public function findBasedOnTokens(string $token, ?string $refresh): ?UsedToken
    {
        $query = $this->createQueryBuilder('ut')
            ->select('ut')
            ->where('ut.token = :token')
            ->orWhere('ut.refreshToken = :refresh')
            ->setParameters([ 'token' => $token, 'refresh' => $refresh ])
            ->getQuery();

        $result = null;
        try {
            $result = $query->getSingleResult();
        } catch (NoResultException) {
            // If there's no result, then we return null
        }
        return $result;
    }
}
