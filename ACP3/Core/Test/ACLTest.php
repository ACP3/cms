<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test;


use ACP3\Core\ACL;
use ACP3\Core\Modules;
use ACP3\Core\User;
use ACP3\Modules\ACP3\Permissions\Cache;
use ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository;
use ACP3\Modules\ACP3\Permissions\Model\RoleRepository;
use ACP3\Modules\ACP3\Permissions\Model\UserRoleRepository;

/**
 * Class ACLTest
 * @package ACP3\Core\Test
 */
class ACLTest extends \PHPUnit_Framework_TestCase
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
    private $roleRepositoryMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $userRoleRepositoryMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $privilegeRepositoryMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $permissionsCacheMock;

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->acl = new ACL(
            $this->userMock,
            $this->modulesMock,
            $this->roleRepositoryMock,
            $this->userRoleRepositoryMock,
            $this->privilegeRepositoryMock,
            $this->permissionsCacheMock
        );
    }

    private function initializeMockObjects()
    {
        $this->userMock = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMethods(['isSuperUser', 'getUserId'])
            ->getMock();
        $this->modulesMock = $this->getMockBuilder(Modules::class)
            ->disableOriginalConstructor()
            ->setMethods(['controllerActionExists', 'isActive'])
            ->getMock();
        $this->roleRepositoryMock = $this->getMockBuilder(RoleRepository::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->userRoleRepositoryMock = $this->getMockBuilder(UserRoleRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRolesByUserId'])
            ->getMock();
        $this->privilegeRepositoryMock = $this->getMockBuilder(PrivilegeRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllPrivileges'])
            ->getMock();
        $this->permissionsCacheMock = $this->getMockBuilder(Cache::class)
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

        $this->setUpUserRoleExpectation($userId);
        $this->assertEquals($expected, $this->acl->getUserRoleIds($userId));
    }

    /**
     * @param $userId
     */
    private function setUpUserRoleExpectation($userId)
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

        $this->setUpUserRoleExpectation($userId);
        $this->assertEquals($expected, $this->acl->getUserRoleNames($userId));
    }

    public function testHasPermissionWithEmptyResource()
    {
        $this->assertFalse($this->acl->hasPermission(''));
    }

    public function testHasPermissionWithNotExistingResource()
    {
        $resource = 'frontend/news/index/index/';

        $this->modulesMock->expects($this->once())
            ->method('controllerActionExists')
            ->with($resource)
            ->willReturn(false);

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
     */
    private function setUpModulesMockExpectations(
        $resource,
        $moduleName,
        $returnValueActionExists,
        $returnValueIsActive
    ) {
        $this->modulesMock->expects($this->once())
            ->method('controllerActionExists')
            ->with($resource)
            ->willReturn($returnValueActionExists);
        $this->modulesMock->expects($this->once())
            ->method('isActive')
            ->with($moduleName)
            ->willReturn($returnValueIsActive);
    }

    public function testHasPermissionAlwaysCanAccessUserLoginPage()
    {
        $resource = 'frontend/users/index/login/';

        $this->setUpModulesMockExpectations($resource, 'users', true, true);

        $this->assertTrue($this->acl->hasPermission($resource));
    }

    public function testHasPermission()
    {
        $resource = 'frontend/foo/index/index/';

        $this->setUpModulesMockExpectations($resource, 'foo', true, true);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            1,
            [
                0 => 1
            ],
            true
        );

        $this->userMock->expects($this->once())
            ->method('getUserId')
            ->willReturn(0);

        $this->assertTrue($this->acl->hasPermission($resource));
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

        $this->setUpModulesMockExpectations($resource, 'foo', true, true);
        $this->setUpPermissionsCacheMockExpectations(
            1,
            1,
            [
                0 => 1
            ],
            true
        );

        $this->userMock->expects($this->once())
            ->method('getUserId')
            ->willReturn(0);

        $this->assertTrue($this->acl->hasPermission($resource));
    }

    public function testHasPermissionAlwaysForSuperUser()
    {
        $resource = 'frontend/foo/index/index/';
        $userId = 1;

        $this->setUpModulesMockExpectations($resource, 'foo', true, true);
        $this->setUpUserRoleExpectation($userId);
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
            ->method('getUserId')
            ->willReturn($userId);
        $this->userMock->expects($this->once())
            ->method('isSuperUser')
            ->willReturn(true);

        $this->assertTrue($this->acl->hasPermission($resource));
    }

    public function testUserHasRole()
    {
        $userId = 1;

        $this->userMock->expects($this->once())
            ->method('getUserId')
            ->willReturn($userId);
        $this->setUpUserRoleExpectation($userId);

        $this->assertTrue($this->acl->userHasRole(2));
    }
}
