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
     * @var \ACP3\Modules\ACP3\Articles\Model\ArticleRepository
     */
    protected $articleRepository;
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
     * @var \ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem
     */
    protected $manageMenuItemHelper;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var Menus\Helpers\MenuItemFormFields
     */
    protected $menuItemFormFieldsHelper;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext          $context
     * @param \ACP3\Core\Date                                     $date
     * @param \ACP3\Modules\ACP3\Articles\Model\ArticleRepository $articleRepository
     * @param \ACP3\Modules\ACP3\Articles\Cache                   $articlesCache
     * @param \ACP3\Modules\ACP3\Articles\Validator               $articlesValidator
     * @param \ACP3\Core\Helpers\FormToken                        $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Articles\Model\ArticleRepository $articleRepository,
        Articles\Cache $articlesCache,
        Articles\Validator $articlesValidator,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
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
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields $menuItemFormFieldsHelper
     *
     * @return $this
     */
    public function setMenuItemFormFieldsHelper(Menus\Helpers\MenuItemFormFields $menuItemFormFieldsHelper)
    {
        $this->menuItemFormFieldsHelper = $menuItemFormFieldsHelper;

        return $this;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem $manageMenuItemHelper
     *
     * @return $this
     */
    public function setManageMenuItemHelper(Menus\Helpers\ManageMenuItem $manageMenuItemHelper)
    {
        $this->manageMenuItemHelper = $manageMenuItemHelper;

        return $this;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository $menuItemRepository
     *
     * @return $this
     */
    public function setMenuItemRepository(Menus\Model\MenuItemRepository $menuItemRepository)
    {
        $this->menuItemRepository = $menuItemRepository;

        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_createPost($this->request->getPost()->all());
        }

        if ($this->acl->hasPermission('admin/menus/items/create') === true) {
            $lang_options = [$this->lang->t('articles', 'create_menu_item')];
            $this->view->assign('options', $this->get('core.helpers.forms')->checkboxGenerator('create', [1], $lang_options, 0));
            $this->view->assign($this->menuItemFormFieldsHelper->createMenuItemFormFields());
        }

        $defaults = [
            'title' => '',
            'text' => '',
            'start' => '',
            'end' => ''
        ];

        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->all()));

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _createPost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->articlesValidator->validate($formData);

            $insertValues = [
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'user_id' => $this->user->getUserId(),
            ];

            $lastId = $this->articleRepository->insert($insertValues);

            $this->seo->insertUriAlias(sprintf(Articles\Helpers::URL_KEY_PATTERN, $lastId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->createOrUpdateMenuItem($formData, $lastId);

            $this->formTokenHelper->unsetFormToken();

            return $lastId;
        });
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;

                foreach ($items as $item) {
                    $uri = sprintf(Articles\Helpers::URL_KEY_PATTERN, $item);

                    $bool = $this->articleRepository->delete($item);

                    if ($this->manageMenuItemHelper) {
                        $this->manageMenuItemHelper->manageMenuItem($uri, false);
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $article = $this->articleRepository->getOneById($id);

        if (empty($article) === false) {
            $this->breadcrumb->setTitlePostfix($article['title']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_editPost($this->request->getPost()->all(), $id);
            }

            if ($this->acl->hasPermission('admin/menus/items/create') === true) {
                $menuItem = $this->menuItemRepository->getOneMenuItemByUri(sprintf(Articles\Helpers::URL_KEY_PATTERN, $id));

                $lang_options = [$this->lang->t('articles', 'create_menu_item')];
                $this->view->assign('options', $this->get('core.helpers.forms')->checkboxGenerator('create', [1], $lang_options, !empty($menuItem) ? 1 : 0));

                $this->view->assign(
                    $this->menuItemFormFieldsHelper->createMenuItemFormFields(
                        $menuItem['block_id'],
                        $menuItem['parent_id'],
                        $menuItem['left_id'],
                        $menuItem['right_id'],
                        $menuItem['display']
                    )
                );
            }

            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields(sprintf(Articles\Helpers::URL_KEY_PATTERN, $id)));

            $this->view->assign('form', array_merge($article, $this->request->getPost()->all()));

            $this->formTokenHelper->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array $formData
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _editPost(array $formData, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $id) {
            $this->articlesValidator->validate(
                $formData,
                sprintf(Articles\Helpers::URL_KEY_PATTERN, $id)
            );

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'user_id' => $this->user->getUserId(),
            ];

            $bool = $this->articleRepository->update($updateValues, $id);

            $this->seo->insertUriAlias(
                sprintf(Articles\Helpers::URL_KEY_PATTERN, $id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->articlesCache->saveCache($id);

            $this->createOrUpdateMenuItem($formData, $id);

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }

    public function actionIndex()
    {
        $articles = $this->articleRepository->getAllInAcp();

        /** @var Core\Helpers\DataGrid $dataTable */
        $dataTable = $this->get('core.helpers.data_grid');
        $dataTable
            ->setResults($articles)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/articles/index/delete')
            ->setResourcePathEdit('admin/articles/index/edit');

        $dataTable
            ->addColumn([
                'label' => $this->lang->t('system', 'publication_period'),
                'type' => 'date_range',
                'fields' => ['start', 'end']
            ], 30)
            ->addColumn([
                'label' => $this->lang->t('articles', 'title'),
                'type' => 'text',
                'fields' => ['title'],
                'default_sort' => true
            ], 20)
            ->addColumn([
                'label' => $this->lang->t('system', 'id'),
                'type' => 'integer',
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataTable->generateDataTable(),
            'show_mass_delete_button' => count($articles) > 0
        ];
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

                $this->manageMenuItemHelper->manageMenuItem(
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
