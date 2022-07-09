<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Entity;

use JetBrains\PhpStorm\Pure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SourceCroc\AccessControlBundle\AccessControl;

#[ORM\Entity]
#[ORM\Table(name: AccessControl::ROLE_TABLE)]
#[ORM\MappedSuperclass]
class Role implements RoleInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
    private ?int $id;

    #[ORM\Column(name: 'identifier', type: 'string', length: 60, unique: true)]
    private string $identifier = 'missing-identifier';

    #[ORM\Column(name: 'name', type: 'string', length: 60)]
    private string $name = 'missing name';

    #[ORM\ManyToOne(targetEntity: Role::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'extends', referencedColumnName: 'id', nullable: true)]
    private ?Role $extends = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
    /** @var Collection<User> $users */
    private Collection $users;

    #[ORM\ManyToMany(targetEntity: Permission::class, fetch: 'EAGER')]
    #[ORM\JoinTable(
        name: '`sourcecroc/access-control/roles_permissions',
        joinColumns: [ new ORM\JoinColumn('user_id') ],
        inverseJoinColumns: [ new ORM\JoinColumn('permission_id') ],
    )]
    /** @var Collection<Permission> $permissions */
    private Collection $permissions;

    #[Pure] public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public function extend(?Role $role): self
    {
        $this->extends = $role;
        return $this;
    }

    public function getInheritsFrom(): ?Role
    {
        return $this->extends;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }
        return $this;
    }

    public function addPermission(PermissionInterface $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }
        return $this;
    }

    public function removePermission(PermissionInterface $permission): self
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
        }
        return $this;
    }

    /* ########################
     * HERE STARTS ENTITY LOGIC
     * ######################## */

    /**
     * Intrinsic object to string conversion
     * @return string
     */
    public function __toString(): string
    {
        return $this->identifier;
    }

    /**
     * Check if this role is (or inherits from the identifier)
     * @param string $identifier
     * @return bool
     */
    public function is(string $identifier): bool
    {
        if ($this->identifier === $identifier) {
            return true;
        }
        return $this->extends !== null && $this->extends->is($identifier);
    }

    /**
     * Check if permission is assigned to this role.
     * @param PermissionInterface|string $permission
     * @return bool
     */
    public function hasPermission(PermissionInterface|string $permission): bool
    {
        $hasPermAssigned = $this->permissions
                ->filter(fn(Permission $perm) => $perm->getIdentifier() === (string)$permission)
                ->count() > 0;
        return $hasPermAssigned || ($this->extends !== null && $this->extends->hasPermission($permission));
    }
}
