<?php

namespace ACP3\Modules\Categories;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'categories';

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang)
    {
        parent::__construct($db, $lang);
    }

    public function resultExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id)) > 0 ? true : false;
    }

    public function resultIsDuplicate($title, $module, $categoryId)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = c.module_id) WHERE c.title = ? AND m.name = ? AND c.id != ?', array($title, $module, $categoryId)) > 0 ? true : false;
    }

    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getAllByModuleName($moduleName)
    {
        return $this->db->fetchAll('SELECT c.* FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = c.module_id) WHERE m.name = ? ORDER BY c.title ASC', array($moduleName));
    }

    public function getAllWithModuleName()
    {
        return $this->db->fetchAll('SELECT c.*, m.name AS module FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = c.module_id) ORDER BY m.name ASC, c.title DESC, c.id DESC');
    }

    public function getModuleNameFromCategoryId($categoryId)
    {
        return $this->db->fetchColumn('SELECT m.name FROM ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m JOIN ' . $this->prefix . static::TABLE_NAME . ' AS c ON(m.id = c.module_id) WHERE c.id = ?', array($categoryId));
    }

    public function getCategoryDeleteInfosById($id) {
        return $this->db->fetchAssoc('SELECT c.picture, m.name AS module FROM ' . $this->prefix . static::TABLE_NAME . ' AS c JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = c.module_id) WHERE c.id = ?', array($id));
    }

    public function validate(array $formData, $file, $settings, $categoryId = '')
    {
        $this->validateFormKey();

        $errors = array();
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('categories', 'title_to_short');
        }
        if (strlen($formData['description']) < 3) {
            $errors['description'] = $this->lang->t('categories', 'description_to_short');
        }
        if (!empty($file) && (empty($file['tmp_name']) || empty($file['size']) ||
                Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
                $_FILES['picture']['error'] !== UPLOAD_ERR_OK)
        ) {
            $errors['picture'] = $this->lang->t('categories', 'invalid_image_selected');
        }
        if (empty($categoryId) && empty($formData['module'])) {
            $errors['module'] = $this->lang->t('categories', 'select_module');
        }

        $categoryName = empty($categoryId) ? $formData['module'] : $this->getModuleNameFromCategoryId($categoryId);
        if (strlen($formData['title']) >= 3 && Helpers::categoryIsDuplicate($formData['title'], $categoryName, $categoryId)) {
            $errors['title'] = $this->lang->t('categories', 'category_already_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::isNumber($formData['width']) === false) {
            $errors['width'] = $this->lang->t('categories', 'invalid_image_width_entered');
        }
        if (Core\Validate::isNumber($formData['height']) === false) {
            $errors['height'] = $this->lang->t('categories', 'invalid_image_height_entered');
        }
        if (Core\Validate::isNumber($formData['filesize']) === false) {
            $errors['filesize'] = $this->lang->t('categories', 'invalid_image_filesize_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    /**
     * Erstellt den Cache für die Kategorien eines Moduls
     *
     * @param string $moduleName
     *  Das Modul, für welches der Kategorien-Cache erstellt werden soll
     * @return boolean
     */
    public function setCache($moduleName)
    {
        return Core\Cache::create($moduleName, $this->getAllByModuleName($moduleName), 'categories');
    }

    /**
     * Gibt die gecacheten Kategorien des jeweiligen Moduls zurück
     *
     * @param string $moduleName
     *  Das jeweilige Modul, für welches die Kategorien geholt werden sollen
     * @return array
     */
    public function getCache($moduleName)
    {
        if (Core\Cache::check($moduleName, 'categories') === false) {
            $this->setCache($moduleName);
        }

        return Core\Cache::output($moduleName, 'categories');
    }

}
