<?php

namespace ACP3\Modules\News;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'news';

    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        parent::__construct($db);
    }

    public function resultExists($id, $time = '')
    {
        $period = empy($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id' . $period, array('id' => $id, 'time' => $time));
    }

    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT n.*, c.title AS category_title FROM ' . $this->prefix . static::TABLE_NAME . ' AS n LEFT JOIN ' . $this->prefix . \ACP3\Modules\Categories\Model::TABLE_NAME . ' AS c ON(n.category_id = c.id) WHERE n.id = ?', array($id));
    }

    public function countAll($time, $categoryId = '')
    {
        if (!empty($categoryId)) {
            $results = $this->getAllByCategoryId($categoryId, $time);
        } else {
            $results = $this->getAll($time, POS);
        }

        return count($results);
    }

    public function getAll($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, array('time' => $time));
    }

    public function getAllByCategoryId($categoryId, $time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE category_id = :categoryId' . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, array('time' => $time, 'categoryId' => $categoryId));
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT n.*, c.title AS cat FROM ' . $this->prefix . static::TABLE_NAME . ' AS n, ' . $this->prefix . \ACP3\Modules\Categories\Model::TABLE_NAME . ' AS c WHERE n.category_id = c.id ORDER BY n.start DESC, n.end DESC, n.id DESC');
    }

    public function validate(array $formData, \ACP3\Core\Lang $lang)
    {
        $this->validateFormKey($lang);

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false) {
            $errors[] = $lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $lang->t('news', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $lang->t('news', 'text_to_short');
        }
        if (strlen($formData['cat_create']) < 3 && \ACP3\Modules\Categories\Helpers::categoryExists($formData['cat']) === false) {
            $errors['cat'] = $lang->t('news', 'select_category');
        }
        if (strlen($formData['cat_create']) >= 3 && \ACP3\Modules\Categories\Helpers::categoryIsDuplicate($formData['cat_create'], 'news') === true) {
            $errors['cat-create'] = $lang->t('categories', 'category_already_exists');
        }
        if (!empty($formData['link_title']) && (empty($formData['uri']) || Core\Validate::isNumber($formData['target']) === false)) {
            $errors[] = $lang->t('news', 'complete_hyperlink_statements');
        }
        if ((bool)CONFIG_SEO_ALIASES === true && !empty($formData['alias']) &&
            (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias']) === true)
        ) {
            $errors['alias'] = $lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateSettings(array $formData, \ACP3\Core\Lang $lang)
    {
        $this->validateFormKey($lang);

        $errors = array();
        if (empty($formData['dateformat']) ||
            ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')
        ) {
            $errors['dateformat'] = $lang->t('system', 'select_date_format');
        }
        if (Core\Validate::isNumber($formData['sidebar']) === false) {
            $errors['sidebar'] = $lang->t('system', 'select_sidebar_entries');
        }
        if (!isset($formData['readmore']) ||
            ($formData['readmore'] != 1 && $formData['readmore'] != 0)
        ) {
            $errors[] = $lang->t('news', 'select_activate_readmore');
        }
        if (Core\Validate::isNumber($formData['readmore_chars']) === false ||
            $formData['readmore_chars'] == 0
        ) {
            $errors['readmore-chars'] = $lang->t('news', 'type_in_readmore_chars');
        }
        if (!isset($formData['category_in_breadcrumb']) ||
            ($formData['category_in_breadcrumb'] != 1 && $formData['category_in_breadcrumb'] != 0)
        ) {
            $errors[] = $lang->t('news', 'select_display_category_in_breadcrumb');
        }
        if (Core\Modules::isActive('comments') === true &&
            (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)
        ) {
            $errors[] = $lang->t('news', 'select_allow_comments');
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
        return Core\Cache::create('details_id_' . $id, $this->getOneById($id), 'news');
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
        if (Core\Cache::check($cacheId, 'news') === false) {
            $this->setCache($id);
        }

        return Core\Cache::output($cacheId, 'news');
    }

}
