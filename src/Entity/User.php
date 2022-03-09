<?php

namespace SourceCroc\AccessControlBundle\Entity;

use JetBrains\PhpStorm\Pure;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SourceCroc\AccessControlBundle\AccessControlConstants;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: "`sourcecroc/access-control/users`")]
#[ORM\MappedSuperclass]
class User implements PermissionContainerInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
    private ?int $id;

    #[ORM\Column(name: 'username', type: 'string', length: 30, unique: true)]
    private ?string $username;

    #[ORM\Column(name: 'secret', type: 'string', length: 255)]
    private ?string $secret;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinTable(
        name: AccessControlConstants::USER_ROLE_TABLE,
        joinColumns: [ new ORM\JoinColumn('user_id') ],
        inverseJoinColumns: [ new ORM\InverseJoinColumn('role_id') ],
    )]
    /** @var Collection<Role> $roles */
    private Collection $roles;

    #[ORM\ManyToMany(targetEntity: Permission::class)]
    #[ORM\JoinTable(
        name: AccessControlConstants::USER_PERMISSION_TABLE,
        joinColumns: [ new ORM\JoinColumn('user_id') ],
        inverseJoinColumns: [ new ORM\InverseJoinColumn('permission_id') ],
    )]
    /** @var Collection<Permission> $permissions */
    private Collection $permissions;

    #[Pure] public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;
        return $this;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
        $role->addUser($this);
        return $this;
    }

    public function removeRole(RoleInterface $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }
        $role->removeUser($this);
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

    public function is(string $identifier): bool {
        return $this->roles->filter(fn(Role $role) => $role->is($identifier))->count() > 0;
    }

    /**
     * Check if permission is assigned to us either via our roles or directly.
     * @param PermissionInterface|string $permission
     * @return bool
     */
    public function hasPermission(PermissionInterface|string $permission): bool
    {
        $hasPermAssigned = $this->permissions
                ->filter(fn(Permission $perm) => $perm->getIdentifier() === (string)$permission)
                ->count() > 0;

        if ($hasPermAssigned) return true;
        return $this->roles->filter(fn(Role $role) => $role->hasPermission($permission))->count() > 0;
    }

    public function getPassword(): ?string
    {
        return $this->secret;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'] + $this->roles->map(fn(RoleInterface $role) => 'ROLE_'.$role->getIdentifier())->toArray();
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
