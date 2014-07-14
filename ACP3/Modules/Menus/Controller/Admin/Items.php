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
     * @var Menus\Model
     */
    protected $menusModel;

    public function __construct(
        Core\Context\Admin $context,
        \Doctrine\DBAL\Connection $db,
        Menus\Model $menusModel)
    {
        parent::__construct($context);

        $this->db = $db;
        $this->menusModel = $menusModel;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            try {
                $validator = $this->get('menus.validator');
                $validator->validateItem($_POST);

                $insertValues = array(
                    'id' => '',
                    'mode' => ($_POST['mode'] == 2 || $_POST['mode'] == 3) && preg_match(Menus\Helpers::ARTICLES_URL_KEY_REGEX, $_POST['uri']) ? '4' : $_POST['mode'],
                    'block_id' => (int)$_POST['block_id'],
                    'parent_id' => (int)$_POST['parent'],
                    'display' => $_POST['display'],
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'uri' => $_POST['mode'] == 1 ? $_POST['module'] : ($_POST['mode'] == 4 ? sprintf(Helpers::URL_KEY_PATTERN, $_POST['articles']) : $_POST['uri']),
                    'target' => $_POST['display'] == 0 ? 1 : $_POST['target'],
                );

                $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
                $bool = $nestedSet->insertNode((int)$_POST['parent'], $insertValues);

                // Verhindern, dass externe URIs Aliase, Keywords, etc. zugewiesen bekommen
                if ($_POST['mode'] != 3) {
                    $path = $_POST['mode'] == 1 ? $_POST['module'] : $_POST['uri'];
                    if ($this->uri->uriAliasExists($_POST['uri'])) {
                        $alias = !empty($_POST['alias']) ? $_POST['alias'] : $this->uri->getUriAlias($_POST['uri']);
                        $keywords = $this->seo->getKeywords($_POST['uri']);
                        $description = $this->seo->getDescription($_POST['uri']);
                    } else {
                        $alias = $_POST['alias'];
                        $keywords = $_POST['seo_keywords'];
                        $description = $_POST['seo_description'];
                    }
                    $this->uri->insertUriAlias(
                        $path,
                        $_POST['mode'] == 1 ? '' : $alias,
                        $keywords,
                        $description,
                        (int)$_POST['seo_robots']
                    );
                    $this->seo->setCache();
                }

                $cache = new Menus\Cache($this->lang, $this->menusModel);
                $cache->setMenuItemsCache();

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/menus');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
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
        $this->view->assign('mode', Core\Functions::selectGenerator('mode', $values_mode, $lang_mode));

        // Menus
        $this->view->assign('blocks', $this->get('menus.helpers')->menusDropdown());

        // Module
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $row) {
            $row['dir'] = strtolower($row['dir']);
            $modules[$row['name']]['selected'] = Core\Functions::selectEntry('module', $row['dir']);
        }
        $this->view->assign('modules', $modules);

        // Ziel des Hyperlinks
        $lang_target = array($this->lang->t('system', 'window_self'), $this->lang->t('system', 'window_blank'));
        $this->view->assign('target', Core\Functions::selectGenerator('target', array(1, 2), $lang_target));

        $lang_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('display', Core\Functions::selectGenerator('display', array(1, 0), $lang_display, 1, 'checked'));

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

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/menus/items/delete', 'acp/menus');

        if ($this->uri->action === 'confirmed') {
            $bool = false;
            $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
            foreach ($items as $item) {
                // URI-Alias löschen
                $itemUri = $this->menusModel->getMenuItemUriById($item);
                $bool = $nestedSet->deleteNode($item);
                $this->uri->deleteUriAlias($itemUri);
            }

            $cache = new Menus\Cache($this->lang, $this->menusModel);
            $cache->setMenuItemsCache();

            $this->seo->setCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/menus');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $menuItem = $this->menusModel->getOneMenuItemById($this->uri->id);

        if (empty($menuItem) === false) {
            $menuItem['alias'] = $menuItem['mode'] == 2 || $menuItem['mode'] == 4 ? $this->uri->getUriAlias($menuItem['uri'], true) : '';
            $menuItem['seo_keywords'] = $this->seo->getKeywords($menuItem['uri']);
            $menuItem['seo_description'] = $this->seo->getDescription($menuItem['uri']);

            if (empty($_POST) === false) {
                try {
                    $validator = $this->get('menus.validator');
                    $validator->validateItem($_POST);

                    // Vorgenommene Änderungen am Datensatz anwenden
                    $mode = ($_POST['mode'] == 2 || $_POST['mode'] == 3) && preg_match(Menus\Helpers::ARTICLES_URL_KEY_REGEX, $_POST['uri']) ? '4' : $_POST['mode'];
                    $uriType = $_POST['mode'] == 4 ? sprintf(Helpers::URL_KEY_PATTERN, $_POST['articles']) : $_POST['uri'];

                    $updateValues = array(
                        'mode' => $mode,
                        'block_id' => $_POST['block_id'],
                        'parent_id' => $_POST['parent'],
                        'display' => $_POST['display'],
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'uri' => $_POST['mode'] == 1 ? $_POST['module'] : $uriType,
                        'target' => $_POST['display'] == 0 ? 1 : $_POST['target'],
                    );

                    $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
                    $bool = $nestedSet->editNode($this->uri->id, (int)$_POST['parent'], (int)$_POST['block_id'], $updateValues);

                    // Verhindern, dass externen URIs Aliase, Keywords, etc. zugewiesen bekommen
                    if ($_POST['mode'] != 3) {
                        $alias = $_POST['alias'] === $menuItem['alias'] ? $menuItem['alias'] : $_POST['alias'];
                        $keywords = $_POST['seo_keywords'] === $menuItem['seo_keywords'] ? $menuItem['seo_keywords'] : $_POST['seo_keywords'];
                        $description = $_POST['seo_description'] === $menuItem['seo_description'] ? $menuItem['seo_description'] : $_POST['seo_description'];
                        $path = $_POST['mode'] == 1 ? $_POST['module'] : $_POST['uri'];
                        $this->uri->insertUriAlias(
                            $path,
                            $_POST['mode'] == 1 ? '' : $alias,
                            $keywords,
                            $description,
                            (int)$_POST['seo_robots']
                        );
                        $this->seo->setCache();
                    }

                    $cache = new Menus\Cache($this->lang, $this->menusModel);
                    $cache->setMenuItemsCache();

                    $this->session->unsetFormToken();

                    $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/menus');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                    $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
                }
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
            $this->view->assign('mode', Core\Functions::selectGenerator('mode', $modeValues, $modeLang, $menuItem['mode']));

            // Block
            $this->view->assign('blocks', $this->get('menus.helpers')->menusDropdown($menuItem['block_id']));

            // Module
            $modules = $this->modules->getAllModules();
            foreach ($modules as $row) {
                $row['dir'] = strtolower($row['dir']);
                $modules[$row['name']]['selected'] = Core\Functions::selectEntry('module', $row['dir'], $menuItem['mode'] == 1 ? $menuItem['uri'] : '');
            }
            $this->view->assign('modules', $modules);

            // Ziel des Hyperlinks
            $lang_target = array($this->lang->t('system', 'window_self'), $this->lang->t('system', 'window_blank'));
            $this->view->assign('target', Core\Functions::selectGenerator('target', array(1, 2), $lang_target, $menuItem['target']));

            $lang_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('display', Core\Functions::selectGenerator('display', array(1, 0), $lang_display, $menuItem['display'], 'checked'));

            if ($this->modules->isActive('articles') === true) {
                $matches = array();
                if (!empty($_POST) === false && $menuItem['mode'] == 4) {
                    preg_match_all(Menus\Helpers::ARTICLES_URL_KEY_REGEX, $menuItem['uri'], $matches);
                }

                $this->view->assign('articles', $this->get('articles.helpers')->articlesList(!empty($matches[2]) ? $matches[2][0] : ''));
            }

            // Daten an Smarty übergeben
            $this->view->assign('pages_list', $this->get('menus.helpers')->menuItemsList($menuItem['parent_id'], $menuItem['left_id'], $menuItem['right_id']));
            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields($menuItem['uri']));
            $this->view->assign('form', array_merge($menuItem, $_POST));

            $this->session->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionOrder()
    {
        if ($this->get('core.validate')->isNumber($this->uri->id) === true && $this->menusModel->menuItemExists($this->uri->id) === true) {
            $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
            $nestedSet->order($this->uri->id, $this->uri->action);

            $cache = new Menus\Cache($this->lang, $this->menusModel);
            $cache->setMenuItemsCache();

            $this->uri->redirect('acp/menus');
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

}