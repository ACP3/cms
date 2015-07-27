<?php

namespace ACP3\Modules\ACP3\Articles\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model
     */
    protected $articlesModel;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache
     */
    protected $articlesCache;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Validator
     */
    protected $articlesValidator;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers
     */
    protected $menusHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model
     */
    protected $menusModel;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Date                            $date
     * @param \ACP3\Modules\ACP3\Articles\Model          $articlesModel
     * @param \ACP3\Modules\ACP3\Articles\Cache          $articlesCache
     * @param \ACP3\Modules\ACP3\Articles\Validator      $articlesValidator
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Articles\Model $articlesModel,
        Articles\Cache $articlesCache,
        Articles\Validator $articlesValidator,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->articlesModel = $articlesModel;
        $this->articlesCache = $articlesCache;
        $this->articlesValidator = $articlesValidator;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Cache $menusCache
     *
     * @return $this
     */
    public function setMenusCache(Menus\Cache $menusCache)
    {
        $this->menusCache = $menusCache;

        return $this;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Helpers $menusHelpers
     *
     * @return $this
     */
    public function setMenusHelpers(Menus\Helpers $menusHelpers)
    {
        $this->menusHelpers = $menusHelpers;

        return $this;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Model $menusModel
     *
     * @return $this
     */
    public function setMenusModel(Menus\Model $menusModel)
    {
        $this->menusModel = $menusModel;

        return $this;
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        if ($this->acl->hasPermission('admin/menus/items/create') === true) {
            $lang_options = [$this->lang->t('articles', 'create_menu_item')];
            $this->view->assign('options', $this->get('core.helpers.forms')->selectGenerator('create', [1], $lang_options, 0, 'checked'));

            if ($this->menusHelpers) {
                $this->view->assign($this->menusHelpers->createMenuItemFormFields());
            }
        }

        $this->view->assign('publication_period', $this->get('core.helpers.date')->datepicker(['start', 'end']));

        $defaults = [
            'title' => '',
            'text' => ''
        ];

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     */
    protected function _createPost(array $formData)
    {
        $this->handleCreatePostAction(function () use ($formData) {
            $this->articlesValidator->validate($formData);

            $insertValues = [
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'user_id' => $this->auth->getUserId(),
            ];

            $lastId = $this->articlesModel->insert($insertValues);

            $this->seo->insertUriAlias(sprintf(Articles\Helpers::URL_KEY_PATTERN, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->createOrUpdateMenuItem($formData, $lastId);

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            return $lastId;
        });
    }

    /**
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        $this->handleDeleteAction(
            $action,
            function ($items) {
                $bool = false;

                foreach ($items as $item) {
                    $uri = sprintf(Articles\Helpers::URL_KEY_PATTERN, $item);

                    $bool = $this->articlesModel->delete($item);

                    if ($this->menusHelpers) {
                        $this->menusHelpers->manageMenuItem($uri, false);
                    }

                    $this->articlesCache->getCacheDriver()->delete(Articles\Cache::CACHE_ID . $item);
                    $this->seo->deleteUriAlias($uri);
                }

                if ($this->menusCache) {
                    $this->menusCache->saveMenusCache();
                }

                return $bool;
            }
        );
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $article = $this->articlesModel->getOneById($id);

        if (empty($article) === false) {
            $this->breadcrumb->setTitlePostfix($article['title']);

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $id);
            }

            if ($this->acl->hasPermission('admin/menus/items/create') === true &&
                $this->menusHelpers &&
                $this->menusModel
            ) {
                $menuItem = $this->menusModel->getOneMenuItemUri(sprintf(Articles\Helpers::URL_KEY_PATTERN, $id));

                $lang_options = [$this->lang->t('articles', 'create_menu_item')];
                $this->view->assign('options', $this->get('core.helpers.forms')->selectGenerator('create', [1], $lang_options, !empty($menuItem) ? 1 : 0, 'checked'));

                $this->view->assign(
                    $this->menusHelpers->createMenuItemFormFields(
                        $menuItem['block_id'],
                        $menuItem['parent_id'],
                        $menuItem['left_id'],
                        $menuItem['right_id'],
                        $menuItem['display']
                    )
                );
            }

            // Datumsauswahl
            $this->view->assign('publication_period', $this->get('core.helpers.date')->datepicker(['start', 'end'], [$article['start'], $article['end']]));

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields(sprintf(Articles\Helpers::URL_KEY_PATTERN, $id)));

            $this->view->assign('form', array_merge($article, $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array $formData
     * @param int   $id
     */
    protected function _editPost(array $formData, $id)
    {
        $this->handleEditPostAction(function () use ($formData, $id) {
            $this->articlesValidator->validate(
                $formData,
                sprintf(Articles\Helpers::URL_KEY_PATTERN, $id)
            );

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'user_id' => $this->auth->getUserId(),
            ];

            $bool = $this->articlesModel->update($updateValues, $id);

            $this->seo->insertUriAlias(
                sprintf(Articles\Helpers::URL_KEY_PATTERN, $id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->articlesCache->saveCache($id);

            $this->createOrUpdateMenuItem($formData, $id);

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            return $bool;
        });
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

    /**
     * @param array $formData
     * @param int   $id
     */
    protected function createOrUpdateMenuItem(array $formData, $id)
    {
        if ($this->menusCache) {
            if ($this->acl->hasPermission('admin/menus/items/create') === true) {
                $data = [
                    'mode' => 4,
                    'block_id' => $formData['block_id'],
                    'parent_id' => (int)$formData['parent_id'],
                    'display' => $formData['display'],
                    'title' => $formData['title'],
                    'target' => 1
                ];

                $this->menusHelpers->manageMenuItem(
                    sprintf(Articles\Helpers::URL_KEY_PATTERN, $id),
                    isset($formData['create']) === true,
                    $data
                );
            }

            // Refresh the menu items cache
            $this->menusCache->saveMenusCache();
        }
    }
}
