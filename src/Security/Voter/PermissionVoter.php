<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Security\Voter;

use SourceCroc\AccessControlBundle\Entity\User;
use SourceCroc\AccessControlBundle\Provider\PermissionProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PermissionVoter extends Voter
{
    private Security $security;
    private PermissionProviderInterface $permissionProvider;

    public function __construct(Security $security, PermissionProviderInterface $permissionProvider)
    {
        $this->security = $security;
        $this->permissionProvider = $permissionProvider;
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        $user = $this->security->getUser();
        if (!($user instanceof User)) {
            return false;
        }

        return array_key_exists($attribute, $this->permissionProvider->getAllPermissions());
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $this->security->getUser();
        return $user->hasPermission($attribute) || $user->is('developer');
    }
}
