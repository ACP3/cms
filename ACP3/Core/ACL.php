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
     * @var Permissions\Cache
     */
    protected $cache;
    /**
     * @var Permissions\Model
     */
    protected $permissionsModel;
    /**
     * Array mit den jeweiligen Rollen zugewiesenen Berechtigungen
     *
     * @var array
     */
    protected $privileges = array();
    /**
     * Array mit den dem Benutzer zugewiesenen Rollen
     *
     * @var array
     */
    protected $userRoles = array();
    /**
     * Array mit allen registrierten Ressourcen
     *
     * @var array
     */
    protected $resources = array();

    /**
     * Konstruktor - erzeugt die ACL für den jeweiligen User
     *
     * @param Auth $auth
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Auth $auth, \Doctrine\DBAL\Connection $db)
    {
        $this->auth = $auth;
        $this->permissionsModel = new Permissions\Model($db);
        $this->cache = new Permissions\Cache($this->permissionsModel);
        $this->userRoles = $this->getUserRoles($auth->getUserId());
        $this->resources = $this->getResources();
        $this->privileges = $this->getRules($this->userRoles);
    }

    /**
     * Gibt alle in der Datenbank vorhandenen Ressourcen zurück
     *
     * @return array
     */
    public function getResources()
    {
        return $this->cache->getResourcesCache();
    }

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück
     *
     * @param integer $userId
     *    ID des Benutzers, dessen Rollen ausgegeben werden sollen
     * @param integer $mode
     *  1 = IDs der Rollen ausgeben
     *  2 = Namen der Rollen ausgeben
     * @return array
     */
    public function getUserRoles($userId, $mode = 1)
    {
        $field = $mode === 2 ? 'r.name' : 'r.id';
        $key = substr($field, 2);
        $userRoles = $this->permissionsModel->getRolesByUserId($userId);
        $c_userRoles = count($userRoles);
        $roles = array();

        for ($i = 0; $i < $c_userRoles; ++$i) {
            $roles[] = $userRoles[$i][$key];
        }
        return $roles;
    }

    /**
     * Gibt alle existieren Rollen aus
     *
     * @return array
     */
    public function getAllRoles()
    {
        return $this->cache->getRolesCache();
    }

    /**
     * Gibt die Rollen-Berechtigungen aus
     *
     * @param array $roles
     *    Array mit den IDs der Rollen
     * @return boolean
     */
    public function getRules(array $roles)
    {
        return $this->cache->getRulesCache($roles);
    }

    /**
     * Gibt aus ob dem Benutzer die jeweilige Rolle zugeordnet ist
     *
     * @param integer $roleId
     *    ID der zu überprüfenden Rolle
     * @return boolean
     */
    public function userHasRole($roleId)
    {
        return in_array($roleId, $this->userRoles);
    }

    /**
     * Gibt aus, ob ein Benutzer die Berechtigung auf eine Privilegie besitzt
     *
     * @param $module
     * @param string $key
     *    The key of the privilege
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
     * Gibt aus, ob ein Benutzer berichtigt ist, eine Ressource zu betreten
     *
     * @param string $resource
     *    The path of a resource in the format of an internal ACP3 url
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

        if (isset($this->resources[$area][$resource])) {
            $module = $resourceArray[1];
            $key = $this->resources[$area][$resource]['key'];
            return $this->userHasPrivilege($module, $key) === true || $this->auth->isSuperUser() === true;
        }
        return false;
    }
}