<?php
namespace ACP3\Core;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class ACL
 * @package ACP3\Core
 */
class ACL
{
    /**
     * @var \ACP3\Core\User
     */
    protected $user;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    protected $permissionsCache;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\RoleRepository
     */
    protected $roleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\UserRoleRepository
     */
    protected $userRoleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository
     */
    protected $privilegeRepository;
    /**
     * Array mit den jeweiligen Rollen zugewiesenen Berechtigungen
     *
     * @var array
     */
    protected $privileges = [];
    /**
     * Array mit den dem Benutzer zugewiesenen Rollen
     *
     * @var array
     */
    protected $userRoles = [];
    /**
     * Array mit allen registrierten Ressourcen
     *
     * @var array
     */
    protected $resources = [];

    /**
     * @param \ACP3\Core\User                                          $user
     * @param \ACP3\Core\Modules                                       $modules
     * @param \ACP3\Modules\ACP3\Permissions\Model\RoleRepository      $roleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\UserRoleRepository  $userRoleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository $privilegeRepository
     * @param \ACP3\Modules\ACP3\Permissions\Cache                     $permissionsCache
     */
    public function __construct(
        User $user,
        Modules $modules,
        Permissions\Model\RoleRepository $roleRepository,
        Permissions\Model\UserRoleRepository $userRoleRepository,
        Permissions\Model\PrivilegeRepository $privilegeRepository,
        Permissions\Cache $permissionsCache
    ) {
        $this->user = $user;
        $this->modules = $modules;
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->privilegeRepository = $privilegeRepository;
        $this->permissionsCache = $permissionsCache;
    }

    /**
     * Initializes the available user privileges
     */
    protected function getPrivileges()
    {
        if ($this->privileges === []) {
            $this->privileges = $this->getRules($this->getUserRoleIds($this->user->getUserId()));
        }

        return $this->privileges;
    }

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück
     *
     * @param integer $userId
     *
     * @return array
     */
    public function getUserRoleIds($userId)
    {
        if (isset($this->userRoles[$userId]) === false) {
            // Special case for guest user
            if ($userId == 0) {
                $this->userRoles[$userId][] = 1; // @TODO: Add config option for this
            } else {
                $userRoles = $this->userRoleRepository->getRolesByUserId($userId);
                $cUserRoles = count($userRoles);

                for ($i = 0; $i < $cUserRoles; ++$i) {
                    $this->userRoles[$userId][] = $userRoles[$i]['id'];
                }
            }
        }
        return $this->userRoles[$userId];
    }

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück
     *
     * @param integer $userId
     *
     * @return array
     */
    public function getUserRoleNames($userId)
    {
        $roles = [];
        foreach ($this->userRoleRepository->getRolesByUserId($userId) as $userRole) {
            $roles[] = $userRole['name'];
        }
        return $roles;
    }

    /**
     * Gibt alle in der Datenbank vorhandenen Ressourcen zurück
     *
     * @return array
     */
    public function getResources()
    {
        if ($this->resources === []) {
            $this->resources = $this->permissionsCache->getResourcesCache();
        }

        return $this->resources;
    }

    /**
     * Returns the role permissions
     *
     * @param array $roleIds
     *
     * @return array
     */
    public function getRules(array $roleIds)
    {
        return $this->permissionsCache->getRulesCache($roleIds);
    }

    /**
     * @return array
     */
    public function getAllRoles()
    {
        return $this->permissionsCache->getRolesCache();
    }

    /**
     * @return array
     */
    public function getAllPrivileges()
    {
        return $this->privilegeRepository->getAllPrivileges();
    }

    /**
     * @param integer $roleId
     *
     * @return boolean
     */
    public function userHasRole($roleId)
    {
        return in_array($roleId, $this->getUserRoleIds($this->user->getUserId()));
    }

    /**
     * @param string $resource
     *
     * @return boolean
     */
    public function canAccessResource($resource)
    {
        $resourceArray = $this->splitResourcePathInArray($resource);

        $area = $resourceArray[0];
        $resource = $resourceArray[1] . '/' . $resourceArray[2] . '/' . $resourceArray[3] . '/';

        // At least allow users to access the login page
        if ($area === AreaEnum::AREA_FRONTEND && $resource === 'users/index/login/') {
            return true;
        } elseif (isset($this->getResources()[$area][$resource])) {
            $module = $resourceArray[1];
            $privilegeKey = $this->getResources()[$area][$resource]['key'];
            return $this->userHasPrivilege($module, $privilegeKey) === true || $this->user->isSuperUser() === true;
        }

        return false;
    }

    /**
     * @param string $resource
     *
     * @return array
     */
    protected function splitResourcePathInArray($resource)
    {
        $resourceArray = explode('/', $resource);

        if (empty($resourceArray[2]) === true) {
            $resourceArray[2] = 'index';
        }
        if (empty($resourceArray[3]) === true) {
            $resourceArray[3] = 'index';
        }
        return $resourceArray;
    }

    /**
     * Returns, whether the current user has given privilege
     *
     * @param string $module
     * @param string $privilegeKey
     *
     * @return boolean
     */
    public function userHasPrivilege($module, $privilegeKey)
    {
        $privilegeKey = strtolower($privilegeKey);
        if (isset($this->getPrivileges()[$module][$privilegeKey])) {
            return $this->getPrivileges()[$module][$privilegeKey]['access'];
        }
        return false;
    }

    /**
     * Überpüft, ob eine Modulaktion existiert und der Benutzer darauf Zugriff hat
     *
     * @param string $path
     *
     * @return boolean
     */
    public function hasPermission($path)
    {
        if (!empty($path) && $this->modules->controllerActionExists($path) === true) {
            $pathArray = explode('/', $path);

            if ($this->modules->isActive($pathArray[1]) === true) {
                return $this->canAccessResource($path);
            }
        }

        return false;
    }
}
