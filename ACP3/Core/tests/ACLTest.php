<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Controller\Helper\ControllerActionExists;

class ACLTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $userMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $modulesMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $userRoleRepositoryMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $permissionsCacheMock;
    /**
     * @var ControllerActionExists&\PHPUnit_Framework_MockObject_MockObject
     */
    private $controllerActionExistsMock;

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->acl = new ACL(
            $this->userMock,
            $this->modulesMock,
            $this->controllerActionExistsMock,
            $this->userRoleRepositoryMock,
            $this->permissionsCacheMock
        );
    }

    private function initializeMockObjects()
    {
        $this->userMock = $this->createMock(UserModelInterface::class);
        $this->modulesMock = $this->createMock(Modules::class);
        $this->controllerActionExistsMock = $this->createMock(ControllerActionExists::class);
        $this->userRoleRepositoryMock = $this->createMock(ACL\Model\Repository\UserRoleRepositoryInterface::class);
        $this->permissionsCacheMock = $this->createMock(ACL\PermissionCacheInterface::class);
    }

    public function testGetUserRoleIdsForGuest()
    {
        $expected = [0 => 1];
        $this->assertEquals($expected, $this->acl->getUserRoleIds(0));
    }

    public function testGetUserRoleIdsForUser()
    {
        $expected = [
            0 => 2,
            1 => 3,
        ];
        $userId = 1;

        $this->setUpUserRoleExpectations($userId);
        $this->assertEquals($expected, $this->acl->getUserRoleIds($userId));
    }

    private function setUpUserRoleExpectations(int $userId)
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

        $this->userRoleRepositoryMock->expects($this->once())
            ->method('getRolesByUserId')
            ->with($userId)
            ->willReturn($returnValue);
    }

    public function testGetUserRoleName()
    {
        $expected = [
            'Foo',
            'Bar',
        ];
        $userId = 1;

        $this->setUpUserRoleExpectations($userId);
        $this->assertEquals($expected, $this->acl->getUserRoleNames($userId));
    }

    public function testHasPermissionWithEmptyResource()
    {
        $this->assertFalse($this->acl->hasPermission(''));
    }

    public function testHasPermissionWithInvalidResource()
    {
        $resource = 'frontend/news/index/index/';

        $this->setUpControllerActionExistsMockExpectations($resource, false);
        $this->setUpModulesMockExpectations('news', false, 0);

        $this->assertFalse($this->acl->hasPermission($resource));
    }

    public function testHasPermissionWithInActiveModule()
    {
        $resource = 'frontend/news/index/index/';

        $this->setUpControllerActionExistsMockExpectations($resource, true);
        $this->setUpModulesMockExpectations('news', false);

        $this->assertFalse($this->acl->hasPermission($resource));
    }

    /**
     * @param string $resource
     * @param bool   $returnValueActionExists
     */
    private function setUpControllerActionExistsMockExpectations(
        $resource,
        $returnValueActionExists
    ) {
        $this->controllerActionExistsMock->expects($this->once())
            ->method('controllerActionExists')
            ->with($resource)
            ->willReturn($returnValueActionExists);
    }

    private function setUpModulesMockExpectations(
        string $moduleName,
        bool $returnValueIsActive,
        int $callCountIsActive = 1
    ) {
        $this->modulesMock->expects($this->exactly($callCountIsActive))
            ->method('isActive')
            ->with($moduleName)
            ->willReturn($returnValueIsActive);
    }

    public function testHasPermission()
    {
        $resource = 'frontend/foo/index/index/';
        $userId = 0;

        $this->setUpUserMockExpectations($userId);
        $this->setUpControllerActionExistsMockExpectations($resource, true);
        $this->setUpModulesMockExpectations('foo', true);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            1,
            [
                0 => 1,
            ],
            true
        );

        $this->assertTrue($this->acl->hasPermission($resource));
    }

    /**
     * @param int $userId
     * @param int $callCount
     */
    private function setUpUserMockExpectations($userId, $callCount = 1)
    {
        $this->userMock->expects($this->exactly($callCount))
            ->method('getUserId')
            ->willReturn($userId);
    }

    /**
     * @param int  $callCountResourceCache
     * @param int  $callCountRulesCache
     * @param bool $hasAccess
     */
    protected function setUpPermissionsCacheMockExpectations(
        $callCountResourceCache,
        $callCountRulesCache,
        array $returnValueRulesCache,
        $hasAccess
    ) {
        $this->permissionsCacheMock->expects($this->exactly($callCountResourceCache))
            ->method('getResourcesCache')
            ->willReturn([
                'frontend' => [
                    'foo/index/index/' => [
                        'key' => 'view',
                        'access' => ACL\PermissionEnum::PERMIT_ACCESS,
                    ],
                ],
            ]);

        $this->permissionsCacheMock->expects($this->exactly($callCountRulesCache))
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

    public function testHasPermissionWithShortResource()
    {
        $resource = 'frontend/foo/';
        $userId = 0;

        $this->setUpUserMockExpectations($userId);
        $this->setUpControllerActionExistsMockExpectations($resource, true);
        $this->setUpModulesMockExpectations('foo', true);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            1,
            [
                0 => 1,
            ],
            true
        );

        $this->assertTrue($this->acl->hasPermission($resource));
    }

    public function testHasPermissionWithUnregisteredResource()
    {
        $resource = 'frontend/foo/index/details/';
        $userId = 0;

        $this->setUpUserMockExpectations($userId, 0);
        $this->setUpControllerActionExistsMockExpectations($resource, true);
        $this->setUpModulesMockExpectations('foo', true);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            0,
            [
                0 => 1,
            ],
            true
        );

        $this->assertFalse($this->acl->hasPermission($resource));
    }

    public function testHasPermissionAlwaysForSuperUser()
    {
        $resource = 'frontend/foo/index/index/';
        $userId = 1;

        $this->setUpControllerActionExistsMockExpectations($resource, true);
        $this->setUpModulesMockExpectations('foo', true);
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

        $this->userMock->expects($this->once())
            ->method('isSuperUser')
            ->willReturn(true);

        $this->assertTrue($this->acl->hasPermission($resource));
    }

    public function testUserHasRole()
    {
        $userId = 1;

        $this->setUpUserMockExpectations($userId);
        $this->setUpUserRoleExpectations($userId);

        $this->assertTrue($this->acl->userHasRole(2));
    }
}
