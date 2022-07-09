<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use SourceCroc\AccessControlBundle\Command\InitializeAccessControl;
use SourceCroc\AccessControlBundle\Entity\Permission;
use SourceCroc\AccessControlBundle\Provider\PermissionProviderInterface;
use SourceCroc\AccessControlBundle\Repository\PermissionRepository;
use SourceCroc\AccessControlBundle\Repository\RoleRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InitializeAccessControlTest extends TestCase
{
    private static array $outputBuffer = [];

    protected function setUp(): void
    {
        self::$outputBuffer = [];
    }

    /**
     * @return Permission[]
     */
    private function getMockedPermissions(): array
    {
        $testPermission = new Permission();
        $testPermission->setIdentifier('test-permission');
        $testPermission->setName('Test Permission');

        return [$testPermission];
    }

    private function getPermissionProviderMock(): PermissionProviderInterface
    {
        $permissionProviderMock = $this->createMock(PermissionProviderInterface::class);

        $permissionProviderMock
            ->expects($this->any())
            ->method('getAllPermissions')
            ->willReturn([
                'test-permission' => ['Test Permission', 'This is a test permission'],
            ]);

        return $permissionProviderMock;
    }

    private function getPermissionRepositoryMock(): PermissionRepository
    {
        $permissionRepositoryMock = $this->createMock(PermissionRepository::class);

        $permissionRepositoryMock
            ->expects($this->any())
            ->method('list')
            ->willReturn($this->getMockedPermissions());

        return $permissionRepositoryMock;
    }

    private function getRoleRepositoryMock(): RoleRepository
    {
        $roleRepositoryMock = $this->createMock(RoleRepository::class);

        return $roleRepositoryMock;
    }

    private function getOutputInterfaceMock(): OutputInterface
    {
        $outputInterfaceMock = $this->createMock(OutputInterface::class);

        $outputBufferClosure = fn(string $text, int $level = 1) => array_push(self::$outputBuffer, $text);
        $outputInterfaceMock->method('writeln')
            ->willReturnCallback($outputBufferClosure);

        return $outputInterfaceMock;
    }

    public function testInitializeAccessControlCommand()
    {
        $permissionProvider = $this->getPermissionProviderMock();
        $permissionRepositoryMock = $this->getPermissionRepositoryMock();
        $roleRepositoryMock = $this->getRoleRepositoryMock();

        $iacCommand = new InitializeAccessControl(
            $permissionProvider,
            $permissionRepositoryMock,
            $roleRepositoryMock
        );

        $iiMock = $this->createMock(InputInterface::class);
        $outputInterfaceMock = $this->getOutputInterfaceMock();

        $returnCode = $iacCommand->run($iiMock, $outputInterfaceMock);
        $this->assertEquals(0, $returnCode);

        $this->assertEquals(1, count(self::$outputBuffer));

        $message = self::$outputBuffer[0];
        $this->assertStringContainsStringIgnoringCase('no differences found', $message);
    }
}
