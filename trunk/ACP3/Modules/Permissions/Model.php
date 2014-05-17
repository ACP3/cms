<?php
/**
 * Created by PhpStorm.
 * User: goratsch
 * Date: 22.12.13
 * Time: 17:00
 */

namespace ACP3\Modules\Permissions;


use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'acl_roles';
    const TABLE_NAME_PRIVILEGES = 'acl_privileges';
    const TABLE_NAME_RESOURCES = 'acl_resources';
    const TABLE_NAME_RULES = 'acl_rules';
    const TABLE_NAME_USER_ROLES = 'acl_user_roles';

    /**
     * @var \ACP3\Core\URI
     */
    protected $uri;

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang, Core\URI $uri)
    {
        parent::__construct($db, $lang);

        $this->uri = $uri;
    }

    public function roleExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id', array('id' => $id)) > 0 ? true : false;
    }

    protected function roleExistsByName($roleName, $id = '')
    {
        if (Core\Validate::isNumber($id) === true) {
            return !empty($roleName) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id != ? AND name = ?', array($id, $roleName)) == 1 ? true : false;
        } else {
            return !empty($roleName) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE name = ?', array($roleName)) == 1 ? true : false;
        }
    }

    public function resourceExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME_RESOURCES . ' WHERE id = :id', array('id' => $id)) > 0 ? true : false;
    }

    public function getRoleById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getResourceById($id)
    {
        return $this->db->fetchAssoc('SELECT r.page, r.area, r.controller, r.privilege_id, m.name AS module_name FROM ' . $this->prefix . static::TABLE_NAME_RESOURCES . ' AS r JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = r.module_id) WHERE r.id = ?', array($id));
    }

    public function getAllResources()
    {
        return $this->db->fetchAll('SELECT m.id AS module_id, m.name AS module_name, r.id AS resource_id, r.page, r.area, r.controller, r.privilege_id, p.key AS privilege_name FROM ' . $this->prefix . static::TABLE_NAME_RESOURCES . ' AS r JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(r.module_id = m.id) JOIN ' . $this->prefix . static::TABLE_NAME_PRIVILEGES . ' AS p ON(r.privilege_id = p.id) ORDER BY r.module_id ASC, r.area ASC, r.controller ASC, r.page ASC');
    }

    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (!empty($formData['name']) && $this->roleExistsByName($formData['name']) === true) {
            $errors['name'] = $this->lang->t('permissions', 'role_already_exists');
        }
        if (empty($formData['privileges']) || is_array($formData['privileges']) === false) {
            $errors[] = $this->lang->t('permissions', 'no_privilege_selected');
        }
        if (!empty($formData['privileges']) && Core\Validate::aclPrivilegesExist($formData['privileges']) === false) {
            $errors[] = $this->lang->t('permissions', 'invalid_privileges');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateCreateResource(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['modules']) || Core\Modules::isInstalled($formData['modules']) === false) {
            $errors['modules'] = $this->lang->t('permissions', 'select_module');
        }
        if (empty($formData['area']) || in_array($formData['area'], array('admin', 'frontend', 'sidebar')) === false) {
            $errors['controller'] = $this->lang->t('permissions', 'type_in_area');
        }
        if (empty($formData['controller'])) {
            $errors['controller'] = $this->lang->t('permissions', 'type_in_controller');
        }
        if (empty($formData['resource']) || preg_match('=/=', $formData['resource']) || Core\Validate::isInternalURI(strtolower($formData['modules']) . '/' . $formData['controller'] . '/' . $formData['resource'] . '/') === false) {
            $errors['resource'] = $this->lang->t('permissions', 'type_in_resource');
        }
        if (empty($formData['privileges']) || Core\Validate::isNumber($formData['privileges']) === false) {
            $errors['privileges'] = $this->lang->t('permissions', 'select_privilege');
        }
        if (Core\Validate::isNumber($formData['privileges']) && $this->resourceExists($formData['privileges']) === false) {
            $errors['privileges'] = $this->lang->t('permissions', 'privilege_does_not_exist');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['name'])) {
            $errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (!empty($formData['name']) && $this->roleExistsByName($formData['name'], $this->uri->id) === true) {
            $errors['name'] = $this->lang->t('permissions', 'role_already_exists');
        }
        if (empty($formData['privileges']) || is_array($formData['privileges']) === false) {
            $errors[] = $this->lang->t('permissions', 'no_privilege_selected');
        }
        if (!empty($formData['privileges']) && Core\Validate::aclPrivilegesExist($formData['privileges']) === false) {
            $errors[] = $this->lang->t('permissions', 'invalid_privileges');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateEditResource(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['modules']) || Core\Modules::isInstalled($formData['modules']) === false) {
            $errors['modules'] = $this->lang->t('permissions', 'select_module');
        }
        if (empty($formData['area']) || in_array($formData['area'], array('admin', 'frontend', 'sidebar')) === false) {
            $errors['controller'] = $this->lang->t('permissions', 'type_in_area');
        }
        if (empty($formData['controller'])) {
            $errors['controller'] = $this->lang->t('permissions', 'type_in_controller');
        }
        if (empty($formData['resource']) || preg_match('=/=', $formData['resource']) || Core\Validate::isInternalURI($formData['modules'] . '/' . $formData['controller'] . '/' . $formData['resource'] . '/') === false) {
            $errors['resource'] = $this->lang->t('permissions', 'type_in_resource');
        }
        if (empty($formData['privileges']) || Core\Validate::isNumber($formData['privileges']) === false) {
            $errors['privileges'] = $this->lang->t('permissions', 'select_privilege');
        }
        if (Core\Validate::isNumber($formData['privileges']) && $this->resourceExists($formData['privileges']) === false) {
            $errors['privileges'] = $this->lang->t('permissions', 'privilege_does_not_exist');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

}
