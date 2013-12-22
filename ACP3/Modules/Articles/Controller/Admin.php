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
    /**
     *
     * @var Model
     */
    protected $model;

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

        $this->model = new Articles\Model($this->db);
    }

    public function actionCreate()
    {
        $access_to_menus = Core\Modules::hasPermission('menus', 'acp_create_item');

        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validateCreate($_POST, $this->lang);

                $insertValues = array(
                    'id' => '',
                    'start' => $this->date->toSQL($_POST['start']),
                    'end' => $this->date->toSQL($_POST['end']),
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'text' => Core\Functions::strEncode($_POST['text'], true),
                    'user_id' => $this->auth->getUserId(),
                );

                $lastId = $this->model->insert($insertValues);
                if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias'])) {
                    Core\SEO::insertUriAlias('articles/details/id_' . $lastId, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);
                }

                if (isset($_POST['create']) === true && $access_to_menus === true) {
                    $insertValues = array(
                        'id' => '',
                        'mode' => 4,
                        'block_id' => $_POST['block_id'],
                        'parent_id' => (int)$_POST['parent'],
                        'display' => $_POST['display'],
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'uri' => 'articles/details/id_' . $lastId . '/',
                        'target' => 1,
                    );

                    $nestedSet = new Core\NestedSet($this->db, \ACP3\Modules\Menus\Model::TABLE_NAME_ITEMS, true);
                    $lastId = $nestedSet->insertNode((int)$_POST['parent'], $insertValues);
                    \ACP3\Modules\Menus\Helpers::setMenuItemsCache();
                }

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/articles');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/articles');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

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

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/articles/delete', 'acp/articles');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;

            $menuModel = new \ACP3\Modules\Menus\Model($this->db);
            $nestedSet = new Core\NestedSet($this->db, \ACP3\Modules\Menus\Model::TABLE_NAME_ITEMS, true);
            foreach ($items as $item) {
                $uri = 'articles/details/id_' . $item . '/';
                $bool = $this->model->delete($item);
                $nestedSet->deleteNode($menuModel->getMenuItemIdByUri($uri));

                Core\Cache::delete('list_id_' . $item, 'articles');
                Core\SEO::deleteUriAlias($uri);
            }

            if (Core\Modules::isInstalled('menus') === true) {
                $menuModel->setMenuItemsCache();
            }

            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/articles');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        $article = $this->model->getOneById($this->uri->id);

        if (empty($article) === false) {
            if (isset($_POST['submit']) === true) {
                try {
                    $this->model->validateEdit($_POST, $this->lang);

                    $updateValues = array(
                        'start' => $this->date->toSQL($_POST['start']),
                        'end' => $this->date->toSQL($_POST['end']),
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'text' => Core\Functions::strEncode($_POST['text'], true),
                        'user_id' => $this->auth->getUserId(),
                    );

                    $bool = $this->model->update($updateValues, $this->uri->id);

                    if ((bool)CONFIG_SEO_ALIASES === true && !empty($_POST['alias'])) {
                        Core\SEO::insertUriAlias('articles/details/id_' . $this->uri->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int)$_POST['seo_robots']);
                    }

                    $this->model->setCache($this->uri->id);

                    // Aliase in der Navigation aktualisieren
                    \ACP3\Modules\Menus\Helpers::setMenuItemsCache();

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/articles');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/articles');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            // Datumsauswahl
            $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end'), array($article['start'], $article['end'])));

            $this->view->assign('SEO_FORM_FIELDS', Core\SEO::formFields('articles/details/id_' . $this->uri->id));

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $article);

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $articles = $this->model->getAllInAcp();
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
