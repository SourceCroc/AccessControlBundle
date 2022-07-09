<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Entity;

use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use SourceCroc\AccessControlBundle\AccessControl;

#[ORM\Entity]
#[ORM\Table(name: AccessControl::PERMISSION_TABLE)]
#[ORM\MappedSuperclass]
class Permission implements PermissionInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
    private ?int $id;

    #[ORM\Column(name: 'identifier', type: 'string', length: 60, unique: true)]
    private string $identifier = 'missing-identifier';

    #[ORM\Column(name: 'name', type: 'string', length: 60)]
    private string $name = 'missing-name';

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /* ########################
     * HERE STARTS ENTITY LOGIC
     * ######################## */

    /**
     * Intrinsic object to string conversion
     * @return string
     */
    #[Pure] public function __toString(): string
    {
        return $this->getIdentifier();
    }
}
