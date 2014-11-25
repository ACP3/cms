<?php
namespace ACP3\Core;

use ACP3\Modules\Permissions;

/**
 * Access control lists
 *
 * @author Tino Goratsch
 */
class ACL
{
    /**
     * @var Auth
     */
    protected $auth;
    /**
     * @var Modules
     */
    protected $modules;
    /**
     * @var Permissions\Cache
     */
    protected $permissionsCache;
    /**
     * @var Permissions\Model
     */
    protected $permissionsModel;
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
     * @param Auth $auth
     * @param Modules $modules
     * @param Permissions\Model $permissionsModel
     * @param Permissions\Cache $permissionsCache
     */
    public function __construct(
        Auth $auth,
        Modules $modules,
        Permissions\Model $permissionsModel,
        Permissions\Cache $permissionsCache
    ) {
        $this->auth = $auth;
        $this->modules = $modules;
        $this->permissionsModel = $permissionsModel;
        $this->permissionsCache = $permissionsCache;

        $this->userRoles = $this->getUserRoles($auth->getUserId());
        $this->resources = $this->getResources();
        $this->privileges = $this->getRules($this->userRoles);
    }

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück
     *
     * @param integer $userId
     *    ID des Benutzers, dessen Rollen ausgegeben werden sollen
     * @param integer $mode
     *    1 = IDs der Rollen ausgeben
     *    2 = Namen der Rollen ausgeben
     *
     * @return array
     */
    public function getUserRoles($userId, $mode = 1)
    {
        $field = $mode === 2 ? 'r.name' : 'r.id';
        $key = substr($field, 2);
        $userRoles = $this->permissionsModel->getRolesByUserId($userId);
        $c_userRoles = count($userRoles);
        $roles = [];

        for ($i = 0; $i < $c_userRoles; ++$i) {
            $roles[] = $userRoles[$i][$key];
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
        return $this->permissionsCache->getResourcesCache();
    }

    /**
     * Returns the role permissions
     *
     * @param array $roleIds
     *
     * @return boolean
     */
    public function getRules(array $roleIds)
    {
        return $this->permissionsCache->getRulesCache($roleIds);
    }

    /**
     * Returns all existing roles
     *
     * @return array
     */
    public function getAllRoles()
    {
        return $this->permissionsCache->getRolesCache();
    }

    /**
     * Returns all existing privileges
     *
     * @return array
     */
    public function getAllPrivileges()
    {
        return $this->permissionsModel->getAllPrivileges();
    }

    /**
     * Gibt zurück ob dem Benutzer die jeweilige Rolle zugeordnet ist
     *
     * @param integer $roleId
     *    ID der zu überprüfenden Rolle
     *
     * @return boolean
     */
    public function userHasRole($roleId)
    {
        return in_array($roleId, $this->userRoles);
    }

    /**
     * Gibt zurück, ob ein Benutzer berichtigt ist, eine Ressource zu betreten
     *
     * @param string $resource
     *    The path of a resource in the format of an internal ACP3 url
     *
     * @return boolean
     */
    public function canAccessResource($resource)
    {
        $resourceArray = explode('/', $resource);

        if (empty($resourceArray[2]) === true) {
            $resourceArray[2] = 'index';
        }
        if (empty($resourceArray[3]) === true) {
            $resourceArray[3] = 'index';
        }

        $area = $resourceArray[0];
        $resource = $resourceArray[1] . '/' . $resourceArray[2] . '/' . $resourceArray[3] . '/';

        // At least allow users to access the login page
        if ($area === 'frontend' && $resource === 'users/index/login/') {
            return true;
        } elseif (isset($this->resources[$area][$resource])) {
            $module = $resourceArray[1];
            $key = $this->resources[$area][$resource]['key'];
            return $this->userHasPrivilege($module, $key) === true || $this->auth->isSuperUser() === true;
        }
        return false;
    }

    /**
     * Gibt zurück, ob ein Benutzer die Berechtigung auf eine Privilegie besitzt
     *
     * @param        $module
     * @param string $key
     *    The key of the privilege
     *
     * @return boolean
     */
    public function userHasPrivilege($module, $key)
    {
        $key = strtolower($key);
        if (isset($this->privileges[$module][$key])) {
            return $this->privileges[$module][$key]['access'];
        }
        return false;
    }

    /**
     * Überpüft, ob eine Modulaktion existiert und der Benutzer darauf Zugriff hat
     *
     * @param string $path
     *    Zu überprüfendes Modul
     *
     * @return integer
     */
    public function hasPermission($path)
    {
        if ($this->modules->actionExists($path) === true) {
            $pathArray = explode('/', $path);

            if ($this->modules->isActive($pathArray[1]) === true) {
                return $this->canAccessResource($path);
            }
        }
        return 0;
    }
}
