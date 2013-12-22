<?php

namespace ACP3\Modules\Files;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'files';

    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        parent::__construct($db);
    }

    /**
     * @param int $id
     * @param string $time
     * @return bool
     */
    public function resultExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id' . $period, array('id' => $id, 'time' => $time)) > 0 ? true : false;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT n.*, c.title AS category_title FROM ' . $this->prefix . static::TABLE_NAME . ' AS n LEFT JOIN ' . $this->prefix . \ACP3\Modules\Categories\Model::TABLE_NAME . ' AS c ON(n.category_id = c.id) WHERE n.id = ?', array($id));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getFileById($id)
    {
        return $this->db->fetchColumn('SELECT file FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    /**
     * @param $time
     * @param string $categoryId
     * @return int
     */
    public function countAll($time = '', $categoryId = '')
    {
        if (!empty($categoryId)) {
            $results = $this->getAllByCategoryId($categoryId, $time);
        } else {
            $results = $this->getAll($time);
        }

        return count($results);
    }

    /**
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     * @return array
     */
    public function getAll($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, array('time' => $time));
    }

    /**
     * @param $categoryId
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     * @return array
     */
    public function getAllByCategoryId($categoryId, $time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE category_id = :categoryId' . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, array('time' => $time, 'categoryId' => $categoryId));
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT f.*, c.title AS cat FROM ' . $this->prefix . static::TABLE_NAME . ' AS f, ' . $this->prefix . \ACP3\Modules\Categories\Model::TABLE_NAME . ' AS c WHERE f.category_id = c.id ORDER BY f.start DESC, f.end DESC, f.id DESC');
    }

    public function validateCreate(array $formData, $file, \ACP3\Core\Lang $lang)
    {
        $this->validateFormKey($lang);

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false) {
            $errors[] = $lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['link-title'] = $lang->t('files', 'type_in_title');
        }
        if (isset($formData['external']) && (empty($file) || empty($formData['filesize']) || empty($formData['unit']))) {
            $errors['external'] = $lang->t('files', 'type_in_external_resource');
        }
        if (!isset($formData['external']) &&
            (empty($file['tmp_name']) || empty($file['size']) || $_FILES['file_internal']['error'] !== UPLOAD_ERR_OK)
        ) {
            $errors['file-internal'] = $lang->t('files', 'select_internal_resource');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $lang->t('files', 'description_to_short');
        }
        if (strlen($formData['cat_create']) < 3 && \ACP3\Modules\Categories\Helpers::categoryExists($formData['cat']) === false) {
            $errors['cat'] = $lang->t('files', 'select_category');
        }
        if (strlen($formData['cat_create']) >= 3 && \ACP3\Modules\Categories\Helpers::categoryIsDuplicate($formData['cat_create'], 'files') === true) {
            $errors['cat-create'] = $lang->t('categories', 'category_already_exists');
        }
        if ((bool)CONFIG_SEO_ALIASES === true && !empty($formData['alias']) && (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias']) === true)) {
            $errors['alias'] = $lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateEdit(array $formData, $file, \ACP3\Core\Lang $lang)
    {
        $this->validateFormKey($lang);

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false) {
            $errors[] = $lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['link-title'] = $lang->t('files', 'type_in_title');
        }
        if (isset($formData['external']) && (empty($file) || empty($formData['filesize']) || empty($formData['unit']))) {
            $errors['external'] = $lang->t('files', 'type_in_external_resource');
        }
        if (!isset($formData['external']) && isset($file) && is_array($file) &&
            (empty($file['tmp_name']) || empty($file['size']) || $_FILES['file_internal']['error'] !== UPLOAD_ERR_OK)
        ) {
            $errors['file-internal'] = $lang->t('files', 'select_internal_resource');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $lang->t('files', 'description_to_short');
        }
        if (strlen($formData['cat_create']) < 3 && \ACP3\Modules\Categories\Helpers::categoryExists($formData['cat']) === false) {
            $errors['cat'] = $lang->t('files', 'select_category');
        }
        if (strlen($formData['cat_create']) >= 3 && \ACP3\Modules\Categories\Helpers::categoryIsDuplicate($formData['cat_create'], 'files') === true) {
            $errors['cat-create'] = $lang->t('categories', 'category_already_exists');
        }
        if ((bool)CONFIG_SEO_ALIASES === true && !empty($formData['alias']) &&
            (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias'], 'files/details/id_' . $this->uri->id) === true)
        ) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateSettings(array $formData, \ACP3\Core\Lang $lang)
    {
        $this->validateFormKey($lang);

        $errors = array();
        if (empty($formData['dateformat']) || ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')) {
            $errors['dateformat'] = $lang->t('system', 'select_date_format');
        }
        if (Core\Validate::isNumber($formData['sidebar']) === false) {
            $errors['sidebar'] = $lang->t('system', 'select_sidebar_entries');
        }
        if (Core\Modules::isActive('comments') === true && (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)) {
            $errors[] = $lang->t('files', 'select_allow_comments');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    /**
     * Erstellt den Cache einer News anhand der angegebenen ID
     *
     * @param integer $id
     *  Die ID der News
     * @return boolean
     */
    public function setCache($id)
    {
        return Core\Cache::create('details_id_' . $id, $this->getOneById($id), 'files');
    }

    /**
     * Bindet die gecachete News ein
     *
     * @param integer $id
     *  Die ID der News
     * @return array
     */
    public function getCache($id)
    {
        $cacheId = 'details_id_' . $id;
        if (Core\Cache::check($cacheId, 'files') === false) {
            $this->setCache($id);
        }

        return Core\Cache::output($cacheId, 'files');
    }

}
