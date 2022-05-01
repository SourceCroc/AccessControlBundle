<?php

namespace SourceCroc\AccessControlBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use SourceCroc\AccessControlBundle\Entity\User;
use SourceCroc\AccessControlBundle\Repository\UsedTokenRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RemoveExpiredUsedTokens extends Command
{
    protected static $defaultName = 'sourcecroc:access-control:remove-expired-used-tokens';

    private UsedTokenRepository $usedTokenRepository;

    public function __construct(UsedTokenRepository $usedTokenRepository)
    {
        parent::__construct(static::$defaultName);
        $this->usedTokenRepository = $usedTokenRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $qb = $this->usedTokenRepository->createQueryBuilder('e')
            ->delete()
            ->where('e.expiresOn <= :now')
            ->setParameter('now', new \DateTimeImmutable('now', new \DateTimeZone('utc')))
            ->getQuery();

        /** @var int $removedTokens */
        $removedTokens = $qb->execute();

        $func = $removedTokens === 0 ? 'warning' : 'success';
        $io->$func(sprintf('Succesfully removed %s used and expired tokens', $removedTokens));
        return 0;
    }
}