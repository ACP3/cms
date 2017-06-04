<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Test\Core\ACL;

use ACP3\Core\ACL;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Permissions\Cache\PermissionsCacheStorage;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class ACLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\ACL\ACLInterface
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

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->acl = new \ACP3\Modules\ACP3\Permissions\Core\ACL\ACL(
            $this->userMock,
            $this->modulesMock,
            $this->userRoleRepositoryMock,
            $this->permissionsCacheMock
        );
    }

    private function initializeMockObjects()
    {
        $this->userMock = $this->getMockBuilder(UserModel::class)
            ->disableOriginalConstructor()
            ->setMethods(['isSuperUser', 'getUserId'])
            ->getMock();
        $this->modulesMock = $this->getMockBuilder(Modules::class)
            ->disableOriginalConstructor()
            ->setMethods(['controllerActionExists', 'isActive'])
            ->getMock();
        $this->userRoleRepositoryMock = $this->getMockBuilder(ACL\Model\Repository\AclUserRolesRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRolesByUserId'])
            ->getMock();
        $this->permissionsCacheMock = $this->getMockBuilder(PermissionsCacheStorage::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResourcesCache', 'getRolesCache', 'getRulesCache'])
            ->getMock();
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
            1 => 3
        ];
        $userId = 1;

        $this->setUpUserRoleExpectations($userId);
        $this->assertEquals($expected, $this->acl->getUserRoleIds($userId));
    }

    /**
     * @param $userId
     */
    private function setUpUserRoleExpectations($userId)
    {
        $returnValue = [
            [
                'id' => 2,
                'name' => 'Foo'
            ],
            [
                'id' => 3,
                'name' => 'Bar'
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
            'Bar'
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

        $this->setUpModulesMockExpectations($resource, 'news', false, false, 0);

        $this->assertFalse($this->acl->hasPermission($resource));
    }

    public function testHasPermissionWithInActiveModule()
    {
        $resource = 'frontend/news/index/index/';

        $this->setUpModulesMockExpectations($resource, 'news', true, false);

        $this->assertFalse($this->acl->hasPermission($resource));
    }

    /**
     * @param string $resource
     * @param string $moduleName
     * @param bool   $returnValueActionExists
     * @param bool   $returnValueIsActive
     * @param int    $callCountIsActive
     */
    private function setUpModulesMockExpectations(
        $resource,
        $moduleName,
        $returnValueActionExists,
        $returnValueIsActive,
        $callCountIsActive = 1
    ) {
        $this->modulesMock->expects($this->once())
            ->method('controllerActionExists')
            ->with($resource)
            ->willReturn($returnValueActionExists);
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
        $this->setUpModulesMockExpectations($resource, 'foo', true, true);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            1,
            [
                0 => 1
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
     * @param int   $callCountResourceCache
     * @param int   $callCountRulesCache
     * @param array $returnValueRulesCache
     * @param bool  $hasAccess
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
                        'access' => ACL\PermissionEnum::PERMIT_ACCESS
                    ]
                ]
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
                    ]
                ]
            ]);
    }

    public function testHasPermissionWithShortResource()
    {
        $resource = 'frontend/foo/';
        $userId = 0;

        $this->setUpUserMockExpectations($userId);
        $this->setUpModulesMockExpectations($resource, 'foo', true, true);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            1,
            [
                0 => 1
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
        $this->setUpModulesMockExpectations($resource, 'foo', true, true);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            0,
            [
                0 => 1
            ],
            true
        );

        $this->assertFalse($this->acl->hasPermission($resource));
    }

    public function testHasPermissionAlwaysForSuperUser()
    {
        $resource = 'frontend/foo/index/index/';
        $userId = 1;

        $this->setUpModulesMockExpectations($resource, 'foo', true, true);
        $this->setUpUserMockExpectations($userId);
        $this->setUpUserRoleExpectations($userId);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            1,
            [
                1 => 3,
                0 => 2
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
