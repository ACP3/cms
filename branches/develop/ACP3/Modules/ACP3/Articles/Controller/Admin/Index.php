<?php

namespace ACP3\Modules\ACP3\Articles\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
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
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;

    /**
     * @param \ACP3\Core\Context\Admin         $context
     * @param \ACP3\Core\Date                  $date
     * @param \ACP3\Modules\ACP3\Articles\Model     $articlesModel
     * @param \ACP3\Modules\ACP3\Articles\Cache     $articlesCache
     * @param \ACP3\Modules\ACP3\Articles\Validator $articlesValidator
     * @param \ACP3\Core\Helpers\Secure        $secureHelper
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Articles\Model $articlesModel,
        Articles\Cache $articlesCache,
        Articles\Validator $articlesValidator,
        Core\Helpers\Secure $secureHelper)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->articlesModel = $articlesModel;
        $this->articlesCache = $articlesCache;
        $this->articlesValidator = $articlesValidator;
        $this->secureHelper = $secureHelper;
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
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        if ($this->acl->hasPermission('admin/menus/items/create') === true) {
            $lang_options = [$this->lang->t('articles', 'create_menu_item')];
            $this->view->assign('options', $this->get('core.helpers.forms')->selectGenerator('create', [1], $lang_options, 0, 'checked'));

            if ($this->menusHelpers) {
                $this->view->assign($this->menusHelpers->createMenuItemFormFields());
            }
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

            if ($lastId !== false && $this->acl->hasPermission('admin/menus/items/create') === true) {
                $data = [
                    'mode' => 4,
                    'block_id' => $formData['block_id'],
                    'parent_id' => (int)$formData['parent_id'],
                    'display' => $formData['display'],
                    'title' => Core\Functions::strEncode($formData['title']),
                    'uri' => sprintf(Articles\Helpers::URL_KEY_PATTERN, $lastId),
                    'target' => 1
                ];

                $this->menusHelpers->manageMenuItem(
                    sprintf(Articles\Helpers::URL_KEY_PATTERN, $lastId),
                    isset($formData['create']) === true,
                    $data
                );

                $this->menusCache->setMenuItemsCache();
            }

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->action === 'confirmed') {
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
                $this->menusCache->setMenuItemsCache();
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'));
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $article = $this->articlesModel->getOneById($this->request->id);

        if (empty($article) === false) {
            $this->breadcrumb->setTitlePostfix($article['title']);

            if (empty($_POST) === false) {
                $this->_editPost($_POST);
            }

            if ($this->acl->hasPermission('admin/menus/items/create') === true &&
                $this->menusHelpers &&
                $this->menusModel
            ) {
                $menuItem = $this->menusModel->getOneMenuItemUri(sprintf(Articles\Helpers::URL_KEY_PATTERN, $this->request->id));

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
            $this->articlesValidator->validate(
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

            $this->seo->insertUriAlias(
                sprintf(Articles\Helpers::URL_KEY_PATTERN, $this->request->id),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );

            $this->articlesCache->setCache($this->request->id);

            // Check, if the Menus module is available
            if ($this->menusCache) {
                if ($this->acl->hasPermission('admin/menus/items/create') === true) {
                    $data = [
                        'mode' => 4,
                        'block_id' => $formData['block_id'],
                        'parent_id' => (int)$formData['parent_id'],
                        'display' => $formData['display'],
                        'title' => Core\Functions::strEncode($formData['title']),
                        'uri' => sprintf(Articles\Helpers::URL_KEY_PATTERN, $this->request->id),
                        'target' => 1
                    ];

                    $this->menusHelpers->manageMenuItem(
                        sprintf(Articles\Helpers::URL_KEY_PATTERN, $this->request->id),
                        isset($formData['create']) === true,
                        $data
                    );
                }

                // Refresh the menu items cache
                $this->menusCache->setMenuItemsCache();
            }

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
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
