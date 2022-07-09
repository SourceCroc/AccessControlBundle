<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Entity;

use JetBrains\PhpStorm\Pure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SourceCroc\AccessControlBundle\AccessControl;
use SourceCroc\AccessControlBundle\Helper\RoleHelper;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: AccessControl::USER_TABLE)]
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

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users', fetch: 'EAGER')]
    #[ORM\JoinTable(
        name: AccessControl::USER_ROLE_TABLE,
        joinColumns: [ new ORM\JoinColumn('user_id') ],
        inverseJoinColumns: [ new ORM\InverseJoinColumn('role_id') ],
    )]
    /** @var Collection<Role> $roles */
    private Collection $roles;

    #[ORM\ManyToMany(targetEntity: Permission::class, fetch: 'EAGER')]
    #[ORM\JoinTable(
        name: AccessControl::USER_PERMISSION_TABLE,
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

    public function is(string $identifier): bool
    {
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

        if ($hasPermAssigned) {
            return true;
        }
        return $this->roles->filter(fn(Role $role) => $role->hasPermission($permission))->count() > 0;
    }

    public function getPassword(): ?string
    {
        return $this->secret;
    }

    /**
     * Gets an objects (indirect)roles
     * convents them to ROLE_{role} versions if argument $symfonyRoles is true (default)
     *
     * @param bool $symfonyRoles (default: true) should we transform it to an array of ROLE_* strings?
     * @param bool $includeParents (default: true) should we include all parent roles?
     * @return string[] | Role[] if $symfonyRoles == true then it is a string array otherwise it is an Role array
     */
    public function getRoles(bool $symfonyRoles = true, bool $includeParents = true): array
    {
        $allRoles = $this->roles->toArray();

        if ($symfonyRoles) {
            if ($includeParents) {
                $parentRoles = $this->getIndirectRoles();
                /** @var Role[] $totalRoles */
                $allRoles = array_unique([...$allRoles, ...$parentRoles]);
            }

            return [...array_map(fn(Role $role) => 'ROLE_' . $role->getIdentifier(), $allRoles)];
        }

        return $this->roles->toArray();
    }

    /** @return Role[] */
    public function getDirectRoles(): array
    {
        return $this->getRoles(false, false);
    }

    /** @return Role[] */
    public function getIndirectRoles(): array
    {
        return RoleHelper::resolveParentRoles($this->roles->toArray());
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
