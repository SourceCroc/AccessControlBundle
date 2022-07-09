<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Command;

use SourceCroc\AccessControlBundle\Entity\Permission;
use SourceCroc\AccessControlBundle\Entity\Role;
use SourceCroc\AccessControlBundle\Provider\PermissionProviderInterface;
use SourceCroc\AccessControlBundle\Repository\PermissionRepository;
use SourceCroc\AccessControlBundle\Repository\RoleRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

class InitializeAccessControl extends Command
{
    protected static $defaultName = 'sourcecroc:access-control:init';
    private PermissionProviderInterface $permissionProvider;
    private PermissionRepository $permissionRepository;
    private RoleRepository $roleRepository;

    public function __construct(
        PermissionProviderInterface $permissionProvider,
        PermissionRepository $permissionRepository,
        RoleRepository $roleRepository,
    ) {
        parent::__construct(static::$defaultName);
        $this->permissionProvider = $permissionProvider;
        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->synchronizePermissions($io);

        if (!$this->roleRepository->findBy(['identifier' => 'developer'])) {
            $suRole = new Role();
            $suRole->setIdentifier('developer');
            $suRole->setName('System Developer');
            $this->roleRepository->save($suRole);
        }

        return 0;
    }

    protected function synchronizePermissions(OutputStyle $io): bool
    {
        $availablePermissions = $this->permissionProvider->getAllPermissions();
        $availablePermIds = array_keys($availablePermissions);

        $knownPermissions = $this->permissionRepository->list();
        $knownPermIds = array_map(fn(Permission $perm) => $perm->getIdentifier(), $knownPermissions);

        $synchronicity = array_intersect($availablePermIds, $knownPermIds);
        $toAdd = array_diff($availablePermIds, $synchronicity);
        $toRemove = array_diff($knownPermIds, $synchronicity);

        $differences = count($toAdd) + count($toRemove);
        if ($differences === 0) {
            $io->warning("No differences found!");
            return false;
        }

        $io->caution(sprintf(
            'Found %d differences! Of which %d are additions and %d are removals.',
            $differences,
            count($toAdd),
            count($toRemove),
        ));
        $continue = $io->choice('Do you want to continue?', ['y' => 'yes', 'n' => 'no'], 'no');
        if (!$continue) {
            return false;
        }

        foreach ($toAdd as $identifier) {
            list($name) = $availablePermissions[$identifier];
            $permission = new Permission();
            $permission->setIdentifier($identifier);
            $permission->setName($name);
            $this->permissionRepository->save($permission);
        }

        foreach ($toRemove as $key => $identifier) {
            $permission = $knownPermissions[$key];
            if (!$this->permissionRepository->remove($permission)) {
                $io->warning(sprintf(
                    "Unable to remove permission %s due to it being in use, skipping",
                    $identifier,
                ));
            }
        }

        return true;
    }
}
