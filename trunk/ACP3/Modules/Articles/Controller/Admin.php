<?php

namespace ACP3\Modules\Articles\Controller;

use ACP3\Core;
use ACP3\Modules\Articles;

/**
 * Module controller of the articles backend
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{

    public function __construct(
        \ACP3\Core\Auth $auth,
        \ACP3\Core\Breadcrumb $breadcrumb,
        \ACP3\Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        \ACP3\Core\Lang $lang,
        \ACP3\Core\Session $session,
        \ACP3\Core\URI $uri,
        \ACP3\Core\View $view)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view);
    }

    public function actionCreate()
    {
        $access_to_menus = Core\Modules::hasPermission('menus', 'acp_create_item');

        if (isset($_POST['submit']) === true) {
            if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
                $errors[] = $this->lang->t('system', 'select_date');
            if (strlen($_POST['title']) < 3)
                $errors['title'] = $this->lang->t('articles', 'title_to_short');
            if (strlen($_POST['text']) < 3)
                $errors['text'] = $this->lang->t('articles', 'text_to_short');
            if ($access_to_menus === true && isset($_POST['create']) === true) {
                if ($_POST['create'] == 1) {
                    if (Core\Validate::isNumber($_POST['block_id']) === false)
                        $errors['block-id'] = $this->lang->t('menus', 'select_menu_bar');
                    if (!empty($_POST['parent']) && Core\Validate::isNumber($_POST['parent']) === false)
                        $errors['parent'] = $this->lang->t('menus', 'select_superior_page');
                    if (!empty($_POST['parent']) && Core\Validate::isNumber($_POST['parent']) === true) {
                        // Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
                        $parent_block = $this->db->fetchColumn('SELECT block_id FROM ' . DB_PRE . 'menu_items WHERE id = ?', array($_POST['parent']));
                        if (!empty($parent_block) && $parent_block != $_POST['block_id'])
                            $errors['parent'] = $this->lang->t('menus', 'superior_page_not_allowed');
                    }
                    if ($_POST['display'] != 0 && $_POST['display'] != 1)
                        $errors[] = $this->lang->t('menus', 'select_item_visibility');
                }
            }
            if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
                (Core\Validate::isUriSafe($_POST['alias']) === false || Core\Validate::uriAliasExists($_POST['alias']) === true)
            )
                $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');

            if (isset($errors) === true) {
                $this->view->assign('error_msg', Core\Functions::errorBox($errors));
            } elseif (Core\Validate::formToken() === false) {
                $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
            } else {
                $insert_values = array(
                    'id' => '',
                    'start' => $this->date->toSQL($_POST['start']),
                    'end' => $this->date->toSQL($_POST['end']),
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'text' => Core\Functions::strEncode($_POST['text'], true),
                    'user_id' => $this->auth->getUserId(),
                );

                $this->db->beginTransaction();
                $lastId = $this->db->insert(DB_PRE . 'articles', $insert_values);
                if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias'])) {
                    Core\SEO::insertUriAlias('articles/details/id_' . $lastId, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);
                }
                $this->db->commit();

                if (isset($_POST['create']) === true && $access_to_menus === true) {
                    $insert_values = array(
                        'id' => '',
                        'mode' => 4,
                        'block_id' => $_POST['block_id'],
                        'parent_id' => (int)$_POST['parent'],
                        'display' => $_POST['display'],
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'uri' => 'articles/details/id_' . $lastId . '/',
                        'target' => 1,
                    );

                    $nestedSet = new Core\NestedSet('menu_items', true);
                    $lastId = $nestedSet->insertNode((int)$_POST['parent'], $insert_values);
                    \ACP3\Modules\Menus\Helpers::setMenuItemsCache();
                }

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/articles');
            }
        }
        if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
            if ($access_to_menus === true) {
                $lang_options = array($this->lang->t('articles', 'create_menu_item'));
                $this->view->assign('options', Core\Functions::selectGenerator('create', array(1), $lang_options, 0, 'checked'));

                // Block
                $this->view->assign('blocks', \ACP3\Modules\Menus\Helpers::menusDropdown());

                $lang_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
                $this->view->assign('display', Core\Functions::selectGenerator('display', array(1, 0), $lang_display, 1, 'checked'));

                $this->view->assign('pages_list', \ACP3\Modules\Menus\Helpers::menuItemsList());
            }

            $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end')));

            $defaults = array(
                'title' => '',
                'text' => '',
                'alias' => '',
                'seo_keywords' => '',
                'seo_description' => ''
            );

            $this->view->assign('SEO_FORM_FIELDS', Core\SEO::formFields());

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

            $this->session->generateFormToken();
        }
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/articles/delete', 'acp/articles');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            $nestedSet = new Core\NestedSet('menu_items', true);
            foreach ($items as $item) {
                $bool = $this->db->delete(DB_PRE . 'articles', array('id' => $item));
                $nestedSet->deleteNode($this->db->fetchColumn('SELECT id FROM ' . DB_PRE . 'menu_items WHERE uri = ?', array('articles/details/id_' . $item . '/')));

                Core\Cache::delete('list_id_' . $item, 'articles');
                Core\SEO::deleteUriAlias('articles/details/id_' . $item);
            }

            if (Core\Modules::isInstalled('menus') === true) {
                \ACP3\Modules\Menus\Helpers::setMenuItemsCache();
            }

            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/articles');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        if (Core\Validate::isNumber($this->uri->id) === true &&
            $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = ?', array($this->uri->id)) == 1
        ) {
            if (isset($_POST['submit']) === true) {
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

                if (isset($errors) === true) {
                    $this->view->assign('error_msg', Core\Functions::errorBox($errors));
                } elseif (Core\Validate::formToken() === false) {
                    $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
                } else {
                    $update_values = array(
                        'start' => $this->date->toSQL($_POST['start']),
                        'end' => $this->date->toSQL($_POST['end']),
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'text' => Core\Functions::strEncode($_POST['text'], true),
                        'user_id' => $this->auth->getUserId(),
                    );

                    $bool = $this->db->update(DB_PRE . 'articles', $update_values, array('id' => $this->uri->id));
                    if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
                        Core\SEO::insertUriAlias('articles/details/id_' . $this->uri->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);

                    Articles\Helpers::setArticlesCache($this->uri->id);

                    // Aliase in der Navigation aktualisieren
                    \ACP3\Modules\Menus\Helpers::setMenuItemsCache();

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/articles');
                }
            }
            if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
                $page = Articles\Helpers::getArticlesCache($this->uri->id);

                // Datumsauswahl
                $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end'), array($page['start'], $page['end'])));

                $this->view->assign('SEO_FORM_FIELDS', Core\SEO::formFields('articles/details/id_' . $this->uri->id));

                $this->view->assign('form', isset($_POST['submit']) ? $_POST : $page);

                $this->session->generateFormToken();
            }
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $articles = $this->db->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'articles ORDER BY title ASC');
        $c_articles = count($articles);

        if ($c_articles > 0) {
            $can_delete = Core\Modules::hasPermission('articles', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 2 : 1,
                'sort_dir' => 'asc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::datatable($config));
            for ($i = 0; $i < $c_articles; ++$i) {
                $articles[$i]['period'] = $this->date->formatTimeRange($articles[$i]['start'], $articles[$i]['end']);
            }
            $this->view->assign('articles', $articles);
            $this->view->assign('can_delete', $can_delete);
        }
    }

}
