<?php

namespace ACP3\Modules\Articles\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Articles;
use ACP3\Modules\Menus;

/**
 * Class Index
 * @package ACP3\Modules\Articles\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var Core\DB
     */
    protected $db;
    /**
     * @var Articles\Model
     */
    protected $articlesModel;
    /**
     * @var Articles\Cache
     */
    protected $articlesCache;
    /**
     * @var \ACP3\Modules\Menus\Model
     */
    protected $menusModel;
    /**
     * @var Menus\Cache
     */
    protected $menusCache;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;

    /**
     * @param Core\Context\Admin $context
     * @param Core\Date $date
     * @param Core\DB $db
     * @param Articles\Model $articlesModel
     * @param Articles\Cache $articlesCache
     * @param Menus\Model $menusModel
     * @param Menus\Cache $menusCache
     * @param Core\Helpers\Secure $secureHelper
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\DB $db,
        Articles\Model $articlesModel,
        Articles\Cache $articlesCache,
        Menus\Model $menusModel,
        Menus\Cache $menusCache,
        Core\Helpers\Secure $secureHelper)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->db = $db;
        $this->articlesModel = $articlesModel;
        $this->articlesCache = $articlesCache;
        $this->menusModel = $menusModel;
        $this->menusCache = $menusCache;
        $this->secureHelper = $secureHelper;
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        if ($this->acl->hasPermission('admin/menus/items/create') === true) {
            $lang_options = [$this->lang->t('articles', 'create_menu_item')];
            $this->view->assign('options', $this->get('core.helpers.forms')->selectGenerator('create', [1], $lang_options, 0, 'checked'));

            // Block
            $this->view->assign('blocks', $this->get('menus.helpers')->menusDropdown());

            $lang_display = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('display', $this->get('core.helpers.forms')->selectGenerator('display', [1, 0], $lang_display, 1, 'checked'));

            $this->view->assign('pages_list', $this->get('menus.helpers')->menuItemsList());
        }

        $this->view->assign('publication_period', $this->date->datepicker(['start', 'end']));

        $defaults = [
            'title' => '',
            'text' => ''
        ];

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $this->view->assign('form', array_merge($defaults, $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    /**
     * @param array $formData
     */
    private function _createPost(array $formData)
    {
        try {
            $this->get('articles.validator')->validate($formData);

            $insertValues = [
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'user_id' => $this->auth->getUserId(),
            ];

            $lastId = $this->articlesModel->insert($insertValues);

            $this->aliases->insertUriAlias(sprintf(Articles\Helpers::URL_KEY_PATTERN, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );
            $this->seo->setCache();

            if (isset($formData['create']) === true && $this->acl->hasPermission('admin/menus/items/create') === true) {
                $insertValues = [
                    'id' => '',
                    'mode' => 4,
                    'block_id' => $formData['block_id'],
                    'parent_id' => (int)$formData['parent'],
                    'display' => $formData['display'],
                    'title' => Core\Functions::strEncode($formData['title']),
                    'uri' => sprintf(Articles\Helpers::URL_KEY_PATTERN, $lastId),
                    'target' => 1,
                ];

                $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);
                $lastId = $nestedSet->insertNode((int)$formData['parent'], $insertValues);

                $cacheMenu = $this->get('menus.cache');
                $cacheMenu->setMenuItemsCache();
            }

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'acp/articles');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/articles');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/articles/index/delete', 'acp/articles');

        if ($this->request->action === 'confirmed') {
            $bool = false;

            $nestedSet = new Core\NestedSet($this->db, Menus\Model::TABLE_NAME_ITEMS, true);

            $cache = $this->get('articles.cache.core');
            foreach ($items as $item) {
                $uri = sprintf(Articles\Helpers::URL_KEY_PATTERN, $item);

                $bool = $this->articlesModel->delete($item);
                $nestedSet->deleteNode($this->menusModel->getMenuItemIdByUri($uri));

                $cache->delete(Articles\Cache::CACHE_ID . $item);
                $this->aliases->deleteUriAlias($uri);
            }

            $this->menusCache->setMenuItemsCache();

            $this->seo->setCache();

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/articles');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $article = $this->articlesModel->getOneById($this->request->id);

        if (empty($article) === false) {
            if (empty($_POST) === false) {
                $this->_editPost($_POST);
            }

            // Datumsauswahl
            $this->view->assign('publication_period', $this->date->datepicker(['start', 'end'], [$article['start'], $article['end']]));

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields(sprintf(Articles\Helpers::URL_KEY_PATTERN, $this->request->id)));

            $this->view->assign('form', array_merge($article, $_POST));

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array $formData
     */
    private function _editPost(array $formData)
    {
        try {
            $this->get('articles.validator')->validate(
                $formData,
                sprintf(Articles\Helpers::URL_KEY_PATTERN, $this->request->id)
            );

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'user_id' => $this->auth->getUserId(),
            ];

            $bool = $this->articlesModel->update($updateValues, $this->request->id);

            $this->aliases->insertUriAlias(
                sprintf(Articles\Helpers::URL_KEY_PATTERN, $this->request->id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );
            $this->seo->setCache();

            $this->articlesCache->setCache($this->request->id);

            // Aliase in der Navigation aktualisieren
            $this->menusCache->setMenuItemsCache();

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/articles');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/articles');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    public function actionIndex()
    {
        $articles = $this->articlesModel->getAllInAcp();

        if (count($articles) > 0) {
            $canDelete = $this->acl->hasPermission('admin/articles/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 2 : 1,
                'sort_dir' => 'asc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            ];
            $this->view->assign('datatable_config', $config);
            $this->view->assign('articles', $articles);
            $this->view->assign('can_delete', $canDelete);
        }
    }
}
