<?php

namespace ACP3\Modules\ACP3\Menus\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Items
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin
 */
class Items extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Router\Aliases
     */
    protected $aliases;
    /**
     * @var \ACP3\Core\NestedSet
     */
    protected $nestedSet;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model
     */
    protected $menusModel;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers
     */
    protected $menusHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Validator
     */
    protected $menusValidator;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Helpers
     */
    protected $articlesHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Router\Aliases                  $aliases
     * @param \ACP3\Core\NestedSet                       $nestedSet
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Menus\Model             $menusModel
     * @param \ACP3\Modules\ACP3\Menus\Cache             $menusCache
     * @param \ACP3\Modules\ACP3\Menus\Helpers           $menusHelpers
     * @param \ACP3\Modules\ACP3\Menus\Validator         $menusValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Router\Aliases $aliases,
        Core\NestedSet $nestedSet,
        Core\Helpers\FormToken $formTokenHelper,
        Menus\Model $menusModel,
        Menus\Cache $menusCache,
        Menus\Helpers $menusHelpers,
        Menus\Validator $menusValidator)
    {
        parent::__construct($context);

        $this->aliases = $aliases;
        $this->nestedSet = $nestedSet;
        $this->formTokenHelper = $formTokenHelper;
        $this->menusModel = $menusModel;
        $this->menusCache = $menusCache;
        $this->menusHelpers = $menusHelpers;
        $this->menusValidator = $menusValidator;
    }

    /**
     * @param \ACP3\Modules\ACP3\Articles\Helpers $articlesHelpers
     *
     * @return $this
     */
    public function setArticlesHelpers(Articles\Helpers $articlesHelpers)
    {
        $this->articlesHelpers = $articlesHelpers;

        return $this;
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        $this->view->assign('mode', $this->fetchMenuItemModes());
        $this->view->assign('modules', $this->fetchModules());
        $this->view->assign('target', $this->get('core.helpers.forms')->linkTargetSelectGenerator('target'));

        if ($this->articlesHelpers) {
            $this->view->assign('articles', $this->articlesHelpers->articlesList());
        }

        $defaults = [
            'title' => '',
            'uri' => '',
        ];

        $this->view->assign($this->menusHelpers->createMenuItemFormFields());
        $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields());
        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function($items) {
                $bool = false;

                foreach ($items as $item) {
                    // URI-Alias löschen
                    $itemUri = $this->menusModel->getMenuItemUriById($item);
                    $bool = $this->nestedSet->deleteNode($item, Menus\Model::TABLE_NAME_ITEMS, true);
                    $this->seo->deleteUriAlias($itemUri);
                }

                $this->menusCache->saveMenusCache();

                return $bool;
            },
            null,
            'acp/menus'
        );
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $menuItem = $this->menusModel->getOneMenuItemById($id);

        if (empty($menuItem) === false) {
            $this->breadcrumb->setTitlePostfix($menuItem['title']);

            $menuItem['alias'] = $menuItem['mode'] == 2 || $menuItem['mode'] == 4 ? $this->aliases->getUriAlias($menuItem['uri'], true) : '';
            $menuItem['seo_keywords'] = $this->seo->getKeywords($menuItem['uri']);
            $menuItem['seo_description'] = $this->seo->getDescription($menuItem['uri']);

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $menuItem, $id);
            }

            $this->view->assign('mode', $this->fetchMenuItemModes($menuItem['mode']));
            $this->view->assign('modules', $this->fetchModules($menuItem));
            $this->view->assign('target', $this->get('core.helpers.forms')->linkTargetSelectGenerator('target', $menuItem['target']));

            if ($this->articlesHelpers) {
                $matches = [];
                if ($this->request->getPost()->isEmpty() && $menuItem['mode'] == 4) {
                    preg_match_all(Menus\Helpers::ARTICLES_URL_KEY_REGEX, $menuItem['uri'], $matches);
                }

                $this->view->assign('articles', $this->articlesHelpers->articlesList(!empty($matches[2]) ? $matches[2][0] : ''));
            }

            $this->view->assign(
                $this->menusHelpers->createMenuItemFormFields(
                    $menuItem['block_id'],
                    $menuItem['parent_id'],
                    $menuItem['left_id'],
                    $menuItem['right_id'],
                    $menuItem['display']
                )
            );
            $this->view->assign('SEO_FORM_FIELDS', $this->seo->formFields($menuItem['uri']));
            $this->view->assign('form', array_merge($menuItem, $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionOrder()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->getParameters()->get('id')) === true &&
            $this->menusModel->menuItemExists($this->request->getParameters()->get('id')) === true
        ) {
            $this->nestedSet->sort(
                $this->request->getParameters()->get('id'),
                $this->request->getParameters()->get('action'),
                Menus\Model::TABLE_NAME_ITEMS,
                true
            );

            $this->menusCache->saveMenusCache();

            $this->redirect()->temporary('acp/menus');
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array $formData
     */
    protected function _createPost(array $formData)
    {
        $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $this->menusValidator->validateItem($formData);

                $insertValues = [
                    'id' => '',
                    'mode' => $this->fetchMenuItemModeForSave($formData),
                    'block_id' => (int)$formData['block_id'],
                    'parent_id' => (int)$formData['parent_id'],
                    'display' => $formData['display'],
                    'title' => Core\Functions::strEncode($formData['title']),
                    'uri' => $this->fetchMenuItemUriForSave($formData),
                    'target' => $formData['display'] == 0 ? 1 : $formData['target'],
                ];

                $bool = $this->nestedSet->insertNode(
                    (int)$formData['parent_id'],
                    $insertValues,
                    Menus\Model::TABLE_NAME_ITEMS,
                    true
                );

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
                    $this->seo->insertUriAlias(
                        $path,
                        $formData['mode'] == 1 ? '' : $alias,
                        $keywords,
                        $description,
                        (int)$formData['seo_robots']
                    );
                }

                $this->menusCache->saveMenusCache();

                $this->formTokenHelper->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
            },
            'acp/menus'
        );
    }

    /**
     * @param array $formData
     * @param array $menuItem
     * @param int   $id
     */
    protected function _editPost(array $formData, array $menuItem, $id)
    {
        $this->actionHelper->handlePostAction(
            function () use ($formData, $menuItem, $id) {
                $this->menusValidator->validateItem($formData);

                $updateValues = [
                    'mode' => $this->fetchMenuItemModeForSave($formData),
                    'block_id' => $formData['block_id'],
                    'parent_id' => $formData['parent_id'],
                    'display' => $formData['display'],
                    'title' => Core\Functions::strEncode($formData['title']),
                    'uri' => $this->fetchMenuItemUriForSave($formData),
                    'target' => $formData['display'] == 0 ? 1 : $formData['target'],
                ];

                $bool = $this->nestedSet->editNode(
                    $id,
                    (int)$formData['parent_id'],
                    (int)$formData['block_id'],
                    $updateValues,
                    Menus\Model::TABLE_NAME_ITEMS,
                    true
                );

                // Verhindern, dass externen URIs Aliase, Keywords, etc. zugewiesen bekommen
                if ($formData['mode'] != 3) {
                    $alias = $formData['alias'] === $menuItem['alias'] ? $menuItem['alias'] : $formData['alias'];
                    $keywords = $formData['seo_keywords'] === $menuItem['seo_keywords'] ? $menuItem['seo_keywords'] : $formData['seo_keywords'];
                    $description = $formData['seo_description'] === $menuItem['seo_description'] ? $menuItem['seo_description'] : $formData['seo_description'];
                    $path = $formData['mode'] == 1 ? $formData['module'] : $formData['uri'];
                    $this->seo->insertUriAlias(
                        $path,
                        $formData['mode'] == 1 ? '' : $alias,
                        $keywords,
                        $description,
                        (int)$formData['seo_robots']
                    );
                }

                $this->menusCache->saveMenusCache();

                $this->formTokenHelper->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
            },
            'acp/menus'
        );
    }

    /**
     * @param array $formData
     *
     * @return string
     */
    protected function fetchMenuItemModeForSave(array $formData)
    {
        return ($formData['mode'] == 2 || $formData['mode'] == 3) && preg_match(Menus\Helpers::ARTICLES_URL_KEY_REGEX, $formData['uri']) ? '4' : $formData['mode'];
    }

    /**
     * @param array $formData
     *
     * @return string
     */
    protected function fetchMenuItemUriForSave(array $formData)
    {
        return $formData['mode'] == 1 ? $formData['module'] : ($formData['mode'] == 4 ? sprintf(Articles\Helpers::URL_KEY_PATTERN, $formData['articles']) : $formData['uri']);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function fetchMenuItemModes($value = '')
    {
        $values_mode = [1, 2, 3];
        $lang_mode = [
            $this->lang->t('menus', 'module'),
            $this->lang->t('menus', 'dynamic_page'),
            $this->lang->t('menus', 'hyperlink')
        ];
        if ($this->articlesHelpers) {
            $values_mode[] = 4;
            $lang_mode[] = $this->lang->t('menus', 'article');
        }

        return $this->get('core.helpers.forms')->selectGenerator('mode', $values_mode, $lang_mode, $value);
    }

    /**
     * @param array $menuItem
     *
     * @return array
     */
    protected function fetchModules(array $menuItem = [])
    {
        $modules = $this->modules->getAllModules();
        foreach ($modules as $row) {
            $row['dir'] = strtolower($row['dir']);
            $modules[$row['name']]['selected'] = $this->get('core.helpers.forms')->selectEntry(
                'module',
                $row['dir'],
                !empty($menuItem) && $menuItem['mode'] == 1 ? $menuItem['uri'] : ''
            );
        }
        return $modules;
    }
}
