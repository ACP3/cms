<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Authentication\Model\UserModelInterface;
use PHPUnit\Framework\TestCase;

class ACLTest extends TestCase
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & UserModelInterface
     */
    private $userMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & \ACP3\Core\ACL\Model\Repository\UserRoleRepositoryInterface
     */
    private $userRoleRepositoryMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & \ACP3\Core\ACL\PermissionCacheInterface
     */
    private $permissionsCacheMock;

    protected function setup(): void
    {
        $this->initializeMockObjects();

        $this->acl = new ACL(
            $this->userMock,
            $this->userRoleRepositoryMock,
            $this->permissionsCacheMock
        );
    }

    private function initializeMockObjects(): void
    {
        $this->userMock = $this->createMock(UserModelInterface::class);
        $this->userRoleRepositoryMock = $this->createMock(ACL\Model\Repository\UserRoleRepositoryInterface::class);
        $this->permissionsCacheMock = $this->createMock(ACL\PermissionCacheInterface::class);
    }

    public function testGetUserRoleIdsForGuest(): void
    {
        $expected = [0 => 1];
        self::assertEquals($expected, $this->acl->getUserRoleIds(0));
    }

    public function testGetUserRoleIdsForUser(): void
    {
        $expected = [
            0 => 2,
            1 => 3,
        ];
        $userId = 1;

        $this->setUpUserRoleExpectations($userId);
        self::assertEquals($expected, $this->acl->getUserRoleIds($userId));
    }

    private function setUpUserRoleExpectations(int $userId): void
    {
        $returnValue = [
            [
                'id' => 2,
                'name' => 'Foo',
            ],
            [
                'id' => 3,
                'name' => 'Bar',
            ],
        ];

        $this->userRoleRepositoryMock->expects(self::once())
            ->method('getRolesByUserId')
            ->with($userId)
            ->willReturn($returnValue);
    }

    public function testGetUserRoleName(): void
    {
        $expected = [
            'Foo',
            'Bar',
        ];
        $userId = 1;

        $this->setUpUserRoleExpectations($userId);
        self::assertEquals($expected, $this->acl->getUserRoleNames($userId));
    }

    public function testHasPermissionWithEmptyResource(): void
    {
        self::assertFalse($this->acl->hasPermission(''));
    }

    public function testHasPermissionWithInvalidResource(): void
    {
        $resource = 'frontend/news/index/index/';

        $this->setUpPermissionsCacheMockExpectations(
            1,
            0,
            [
                0 => 1,
            ],
            true
        );

        self::assertFalse($this->acl->hasPermission($resource));
    }

    public function testHasPermission(): void
    {
        $resource = 'frontend/foo/index/index/';
        $userId = 0;

        $this->setUpUserMockExpectations($userId);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            1,
            [
                0 => 1,
            ],
            true
        );

        self::assertTrue($this->acl->hasPermission($resource));
    }

    private function setUpUserMockExpectations(int $userId, int $callCount = 1): void
    {
        $this->userMock->expects(self::exactly($callCount))
            ->method('getUserId')
            ->willReturn($userId);
    }

    protected function setUpPermissionsCacheMockExpectations(
        int $callCountResourceCache,
        int $callCountRulesCache,
        array $returnValueRulesCache,
        bool $hasAccess
    ): void {
        $this->permissionsCacheMock->expects(self::exactly($callCountResourceCache))
            ->method('getResourcesCache')
            ->willReturn([
                'frontend' => [
                    'foo/index/index/' => [
                        'key' => 'view',
                        'access' => ACL\PermissionEnum::PERMIT_ACCESS,
                    ],
                ],
            ]);

        $this->permissionsCacheMock->expects(self::exactly($callCountRulesCache))
            ->method('getRulesCache')
            ->with($returnValueRulesCache)
            ->willReturn([
                'foo' => [
                    'view' => [
                        'id' => ACL\PrivilegeEnum::FRONTEND_VIEW,
                        'description' => '',
                        'permission' => ACL\PermissionEnum::PERMIT_ACCESS,
                        'access' => $hasAccess,
                    ],
                ],
            ]);
    }

    public function testHasPermissionWithShortResource(): void
    {
        $resource = 'frontend/foo/';
        $userId = 0;

        $this->setUpUserMockExpectations($userId);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            1,
            [
                0 => 1,
            ],
            true
        );

        self::assertTrue($this->acl->hasPermission($resource));
    }

    public function testHasPermissionWithUnregisteredResource(): void
    {
        $resource = 'frontend/foo/index/details/';
        $userId = 0;

        $this->setUpUserMockExpectations($userId, 0);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            0,
            [
                0 => 1,
            ],
            true
        );

        self::assertFalse($this->acl->hasPermission($resource));
    }

    public function testHasPermissionAlwaysForSuperUser(): void
    {
        $resource = 'frontend/foo/index/index/';
        $userId = 1;

        $this->setUpUserMockExpectations($userId);
        $this->setUpUserRoleExpectations($userId);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            1,
            [
                1 => 3,
                0 => 2,
            ],
            false
        );

        $this->userMock->expects(self::once())
            ->method('isSuperUser')
            ->willReturn(true);

        self::assertTrue($this->acl->hasPermission($resource));
    }

    public function testUserHasRole(): void
    {
        $userId = 1;

        $this->setUpUserMockExpectations($userId);
        $this->setUpUserRoleExpectations($userId);

        self::assertTrue($this->acl->userHasRole(2));
    }
}
