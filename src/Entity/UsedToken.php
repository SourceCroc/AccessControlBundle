<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SourceCroc\AccessControlBundle\Repository\UsedTokenRepository;

#[ORM\Entity(repositoryClass: UsedTokenRepository::class)]
#[ORM\Table(name: '`sourcecroc/access-control/used_tokens`')]
class UsedToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', unique: true, options: ['unsigned' => true])]
    private ?int $id;

    #[ORM\Column(name: 'token', type: 'string', unique: true, options: ['length' => 511])]
    private string $token;

    #[ORM\Column(name: 'refresh', type: 'string', unique: true, options: ['length' => 511])]
    private string $refreshToken;

    #[ORM\Column(name: 'used_on', type: 'datetime_immutable')]
    private \DateTimeImmutable $usedOn;

    #[ORM\Column(name: 'expires_on', type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresOn;

    public function __construct()
    {
        $this->usedOn = new \DateTimeImmutable('now', new \DateTimeZone('utc'));
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUsedOn(): \DateTimeImmutable
    {
        return $this->usedOn;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpiresOn(): \DateTimeImmutable
    {
        return $this->expiresOn;
    }

    public function setExpiresOn(\DateTimeImmutable $eon): void
    {
        $this->expiresOn = $eon;
    }
}
