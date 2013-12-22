<?php
/**
 * Created by PhpStorm.
 * User: goratsch
 * Date: 22.12.13
 * Time: 17:00
 */

namespace ACP3\Modules\Articles;


use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'articles';

    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        parent::__construct($db);
    }

    public function resultExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id' . $period, array('id' => $id, 'time' => $time)) > 0 ? true : false;
    }

    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function countAll($time = '')
    {
        return count($this->getAll($time));
    }

    public function getAll($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . $where . ' ORDER BY title ASC' . $limitStmt, array('time' => $time));
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY title ASC');
    }

    public function validateCreate(array $formData, \ACP3\Core\Lang $lang)
    {
        $this->validateFormKey($lang);

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false)
            $errors[] = $lang->t('system', 'select_date');
        if (strlen($formData['title']) < 3)
            $errors['title'] = $lang->t('articles', 'title_to_short');
        if (strlen($formData['text']) < 3)
            $errors['text'] = $lang->t('articles', 'text_to_short');
        if ($access_to_menus === true && isset($formData['create']) === true) {
            if ($formData['create'] == 1) {
                if (Core\Validate::isNumber($formData['block_id']) === false)
                    $errors['block-id'] = $lang->t('menus', 'select_menu_bar');
                if (!empty($formData['parent']) && Core\Validate::isNumber($formData['parent']) === false)
                    $errors['parent'] = $lang->t('menus', 'select_superior_page');
                if (!empty($formData['parent']) && Core\Validate::isNumber($formData['parent']) === true) {
                    // Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
                    $parent_block = $this->db->fetchColumn('SELECT block_id FROM ' . DB_PRE . 'menu_items WHERE id = ?', array($formData['parent']));
                    if (!empty($parent_block) && $parent_block != $formData['block_id'])
                        $errors['parent'] = $lang->t('menus', 'superior_page_not_allowed');
                }
                if ($formData['display'] != 0 && $formData['display'] != 1)
                    $errors[] = $lang->t('menus', 'select_item_visibility');
            }
        }
        if ((bool)CONFIG_SEO_ALIASES === true && !empty($formData['alias']) &&
            (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias']) === true)
        )
            $errors['alias'] = $lang->t('system', 'uri_alias_unallowed_characters_or_exists');


        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateEdit(array $formData, \ACP3\Core\Lang $lang) {
        $this->validateFormKey($lang);

        $errors = array();
        if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
            $errors[] = $this->lang->t('system', 'select_date');
        if (strlen($_POST['title']) < 3)
            $errors['title'] = $this->lang->t('articles', 'title_to_short');
        if (strlen($_POST['text']) < 3)
            $errors['text'] = $this->lang->t('articles', 'text_to_short');
        if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
            (Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias'], 'articles/details/id_' . $this->uri->id) === true)
        )
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    /**
     * Erstellt den Cache eines Artikels anhand der angegebenen ID
     *
     * @param integer $id
     *  Die ID der statischen Seite
     * @return boolean
     */
    public function setCache($id)
    {
        return Core\Cache::create('list_id_' . $id, $this->getOneById($id), 'articles');
    }

    /**
     * Bindet den gecacheten Artikel ein
     *
     * @param integer $id
     *  Die ID der statischen Seite
     * @return array
     */
    public function getCache($id)
    {
        if (Core\Cache::check('list_id_' . $id, 'articles') === false) {
            $this->setCache($id);
        }

        return Core\Cache::output('list_id_' . $id, 'articles');
    }

}
