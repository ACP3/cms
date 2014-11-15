<?php

namespace ACP3\Modules\Menus\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Articles\Helpers;
use ACP3\Modules\Menus;

/**
 * Class Items
 * @package ACP3\Modules\Menus\Controller\Admin
 */
class Items extends Core\Modules\Controller\Admin
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Menus\Model
     */
    protected $menusModel;
    /**
     * @var Menus\Cache
     */
    protected $menusCache;

    /**
     * @param Core\Context\Admin $context
     * @param \Doctrine\DBAL\Connection $db
     * @param Core\Helpers\Secure $secureHelper
     * @param Menus\Model $menusModel
     * @param Menus\Cache $menusCache
     */
    public function __construct(
        Core\Context\Admin $context,
        \Doctrine\DBAL\Connection $db,
        Core\Helpers\Secure $secureHelper,
        Menus\Model $menusModel,
        Menus\Cache $menusCache)
    {
        parent::__construct($context);

        $this->db = $db;
        $this->secureHelper = $secureHelper;
        $this->menusModel = $menusModel;
        $this->menusCache = $menusCache;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        // Seitentyp
        $values_mode = array(1, 2, 3);
        $lang_mode = array(
            $this->lang->t('menus', 'module'),
            $this->lang->t('menus', 'dynamic_page'),
            $this->lang->t('menus', 'hyperlink')
        );
        if ($this->modules->isActive('articles')) {
            $values_mode[] = 4;
            $lang_mode[] = $this->lang->t('menus', 'article');
        }
        $this->view->assign('mode', $this->get('core.helpers.forms')->selectGenerator('mode', $values_mode, $lang_mode));

        // Menus
        $this->view->assign('blocks', $this->get('menus.helpers')->menusDropdown());

        // Module
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $row) {
            $row['dir'] = strtolower($row['dir']);
            $modules[$row['name']]['selected'] = $this->get('core.helpers.forms')->selectEntry('module', $row['dir']);
        }
        $this->view->assign('modules', $modules);

        // Ziel des Hyperlinks
        $lang_target = array($this->lang->t('system', 'window_self'), $this->lang->t('system', 'window_blank'));
        $this->view->assign('target', $this->get('core.helpers.forms')->selectGenerator('target', array(1, 2), $lang_target));

        $lang_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('display', $this->get('core.helpers.forms')->selectGenerator('display', array(1, 0), $lang_display, 1, 'checked'));

        if ($this->modules->isActive('articles') === true) {
            $this->view->assign('articles', $this->get('articles.helpers')->articlesList());
        }

        $defaults = array(
            'title' => '',
            'alias' => '',
            'uri' => '',
            'seo_keywords' => '',
            'seo_description' => '',
        );

        // Daten an Smarty übergeben
        $this->view->assign('pages_list', $this->get('menus.helpers')->menuItemsList());
        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());
        $this->view->assign('form', array_merge($defaults, $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/menus/items/delete', 'acp/menus');

        if ($this->request->action === 'confirmed') {
            $bool = false;
            $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
            foreach ($items as $item) {
                // URI-Alias löschen
                $itemUri = $this->menusModel->getMenuItemUriById($item);
                $bool = $nestedSet->deleteNode($item);
                $this->aliases->deleteUriAlias($itemUri);
            }

            $this->menusCache->setMenuItemsCache();

            $this->seo->setCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/menus');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $menuItem = $this->menusModel->getOneMenuItemById($this->request->id);

        if (empty($menuItem) === false) {
            $menuItem['alias'] = $menuItem['mode'] == 2 || $menuItem['mode'] == 4 ? $this->aliases->getUriAlias($menuItem['uri'], true) : '';
            $menuItem['seo_keywords'] = $this->seo->getKeywords($menuItem['uri']);
            $menuItem['seo_description'] = $this->seo->getDescription($menuItem['uri']);

            if (empty($_POST) === false) {
                $this->_editPost($_POST, $menuItem);
            }

            // Seitentyp
            $modeValues = array(1, 2, 3);
            $modeLang = array(
                $this->lang->t('menus', 'module'),
                $this->lang->t('menus', 'dynamic_page'),
                $this->lang->t('menus', 'hyperlink')
            );
            if ($this->modules->isActive('articles')) {
                $modeValues[] = 4;
                $modeLang[] = $this->lang->t('menus', 'article');
            }
            $this->view->assign('mode', $this->get('core.helpers.forms')->selectGenerator('mode', $modeValues, $modeLang, $menuItem['mode']));

            // Block
            $this->view->assign('blocks', $this->get('menus.helpers')->menusDropdown($menuItem['block_id']));

            // Module
            $modules = $this->modules->getAllModules();
            foreach ($modules as $row) {
                $row['dir'] = strtolower($row['dir']);
                $modules[$row['name']]['selected'] = $this->get('core.helpers.forms')->selectEntry('module', $row['dir'], $menuItem['mode'] == 1 ? $menuItem['uri'] : '');
            }
            $this->view->assign('modules', $modules);

            // Ziel des Hyperlinks
            $lang_target = array($this->lang->t('system', 'window_self'), $this->lang->t('system', 'window_blank'));
            $this->view->assign('target', $this->get('core.helpers.forms')->selectGenerator('target', array(1, 2), $lang_target, $menuItem['target']));

            $lang_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('display', $this->get('core.helpers.forms')->selectGenerator('display', array(1, 0), $lang_display, $menuItem['display'], 'checked'));

            if ($this->modules->isActive('articles') === true) {
                $matches = [];
                if (!empty($_POST) === false && $menuItem['mode'] == 4) {
                    preg_match_all(Menus\Helpers::ARTICLES_URL_KEY_REGEX, $menuItem['uri'], $matches);
                }

                $this->view->assign('articles', $this->get('articles.helpers')->articlesList(!empty($matches[2]) ? $matches[2][0] : ''));
            }

            // Daten an Smarty übergeben
            $this->view->assign('pages_list', $this->get('menus.helpers')->menuItemsList($menuItem['parent_id'], $menuItem['left_id'], $menuItem['right_id']));
            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields($menuItem['uri']));
            $this->view->assign('form', array_merge($menuItem, $_POST));

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionOrder()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true && $this->menusModel->menuItemExists($this->request->id) === true) {
            $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
            $nestedSet->order($this->request->id, $this->request->action);

            $this->menusCache->setMenuItemsCache();

            $this->redirect()->temporary('acp/menus');
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array $formData
     */
    private function _createPost(array $formData)
    {
        try {
            $validator = $this->get('menus.validator');
            if ($this->modules->isActive('articles') === true) {
                $validator->setArticlesHelpers($this->get('articles.helpers'));
            }
            $validator->validateItem($formData);

            $insertValues = array(
                'id' => '',
                'mode' => ($formData['mode'] == 2 || $formData['mode'] == 3) && preg_match(Menus\Helpers::ARTICLES_URL_KEY_REGEX, $formData['uri']) ? '4' : $formData['mode'],
                'block_id' => (int)$formData['block_id'],
                'parent_id' => (int)$formData['parent'],
                'display' => $formData['display'],
                'title' => Core\Functions::strEncode($formData['title']),
                'uri' => $formData['mode'] == 1 ? $formData['module'] : ($formData['mode'] == 4 ? sprintf(Helpers::URL_KEY_PATTERN, $formData['articles']) : $formData['uri']),
                'target' => $formData['display'] == 0 ? 1 : $formData['target'],
            );

            $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
            $bool = $nestedSet->insertNode((int)$formData['parent'], $insertValues);

            // Verhindern, dass externe URIs Aliase, Keywords, etc. zugewiesen bekommen
            if ($formData['mode'] != 3) {
                $path = $formData['mode'] == 1 ? $formData['module'] : $formData['uri'];
                if ($this->aliases->uriAliasExists($formData['uri'])) {
                    $alias = !empty($formData['alias']) ? $formData['alias'] : $this->aliases->getUriAlias($formData['uri']);
                    $keywords = $this->seo->getKeywords($formData['uri']);
                    $description = $this->seo->getDescription($formData['uri']);
                } else {
                    $alias = $formData['alias'];
                    $keywords = $formData['seo_keywords'];
                    $description = $formData['seo_description'];
                }
                $this->aliases->insertUriAlias(
                    $path,
                    $formData['mode'] == 1 ? '' : $alias,
                    $keywords,
                    $description,
                    (int)$formData['seo_robots']
                );
                $this->seo->setCache();
            }

            $this->menusCache->setMenuItemsCache();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/menus');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     * @param array $menuItem
     */
    private function _editPost(array $formData, array $menuItem)
    {
        try {
            $validator = $this->get('menus.validator');
            if ($this->modules->isActive('articles') === true) {
                $validator->setArticlesHelpers($this->get('articles.helpers'));
            }
            $validator->validateItem($formData);

            // Vorgenommene Änderungen am Datensatz anwenden
            $mode = ($formData['mode'] == 2 || $formData['mode'] == 3) && preg_match(Menus\Helpers::ARTICLES_URL_KEY_REGEX, $formData['uri']) ? '4' : $formData['mode'];
            $uriType = $formData['mode'] == 4 ? sprintf(Helpers::URL_KEY_PATTERN, $formData['articles']) : $formData['uri'];

            $updateValues = array(
                'mode' => $mode,
                'block_id' => $formData['block_id'],
                'parent_id' => $formData['parent'],
                'display' => $formData['display'],
                'title' => Core\Functions::strEncode($formData['title']),
                'uri' => $formData['mode'] == 1 ? $formData['module'] : $uriType,
                'target' => $formData['display'] == 0 ? 1 : $formData['target'],
            );

            $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
            $bool = $nestedSet->editNode($this->request->id, (int)$formData['parent'], (int)$formData['block_id'], $updateValues);

            // Verhindern, dass externen URIs Aliase, Keywords, etc. zugewiesen bekommen
            if ($formData['mode'] != 3) {
                $alias = $formData['alias'] === $menuItem['alias'] ? $menuItem['alias'] : $formData['alias'];
                $keywords = $formData['seo_keywords'] === $menuItem['seo_keywords'] ? $menuItem['seo_keywords'] : $formData['seo_keywords'];
                $description = $formData['seo_description'] === $menuItem['seo_description'] ? $menuItem['seo_description'] : $formData['seo_description'];
                $path = $formData['mode'] == 1 ? $formData['module'] : $formData['uri'];
                $this->aliases->insertUriAlias(
                    $path,
                    $formData['mode'] == 1 ? '' : $alias,
                    $keywords,
                    $description,
                    (int)$formData['seo_robots']
                );
                $this->seo->setCache();
            }

            $this->menusCache->setMenuItemsCache();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/menus');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

}