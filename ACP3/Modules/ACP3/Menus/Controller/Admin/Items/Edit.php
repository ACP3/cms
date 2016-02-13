<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Items
 */
class Edit extends AbstractFormAction
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
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Validation\MenuItemFormValidation
     */
    protected $menuItemFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields
     */
    protected $menuItemFormFieldsHelper;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext                 $context
     * @param \ACP3\Core\Router\Aliases                                  $aliases
     * @param \ACP3\Core\NestedSet                                       $nestedSet
     * @param \ACP3\Core\Helpers\FormToken                               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository          $menuItemRepository
     * @param \ACP3\Modules\ACP3\Menus\Cache                             $menusCache
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields        $menuItemFormFieldsHelper
     * @param \ACP3\Modules\ACP3\Menus\Validation\MenuItemFormValidation $menuItemFormValidation
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Router\Aliases $aliases,
        Core\NestedSet $nestedSet,
        Core\Helpers\FormToken $formTokenHelper,
        Menus\Model\MenuItemRepository $menuItemRepository,
        Menus\Cache $menusCache,
        Menus\Helpers\MenuItemFormFields $menuItemFormFieldsHelper,
        Menus\Validation\MenuItemFormValidation $menuItemFormValidation
    ) {
        parent::__construct($context);

        $this->aliases = $aliases;
        $this->nestedSet = $nestedSet;
        $this->formTokenHelper = $formTokenHelper;
        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
        $this->menuItemFormFieldsHelper = $menuItemFormFieldsHelper;
        $this->menuItemFormValidation = $menuItemFormValidation;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        $menuItem = $this->menuItemRepository->getOneMenuItemById($id);

        if (empty($menuItem) === false) {
            $this->breadcrumb->setTitlePostfix($menuItem['title']);

            $menuItem['alias'] = $menuItem['mode'] == 2 || $menuItem['mode'] == 4 ? $this->aliases->getUriAlias($menuItem['uri'],
                true) : '';
            $menuItem['seo_keywords'] = $this->seo->getKeywords($menuItem['uri']);
            $menuItem['seo_description'] = $this->seo->getDescription($menuItem['uri']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost($this->request->getPost()->all(), $menuItem, $id);
            }

            if ($this->articlesHelpers) {
                $matches = [];
                if ($this->request->getPost()->isEmpty() && $menuItem['mode'] == 4) {
                    preg_match_all(Menus\Helpers\MenuItemsList::ARTICLES_URL_KEY_REGEX, $menuItem['uri'], $matches);
                }

                $this->view->assign('articles',
                    $this->articlesHelpers->articlesList(!empty($matches[2]) ? $matches[2][0] : ''));
            }

            $this->view->assign(
                $this->menuItemFormFieldsHelper->createMenuItemFormFields(
                    $menuItem['block_id'],
                    $menuItem['parent_id'],
                    $menuItem['left_id'],
                    $menuItem['right_id'],
                    $menuItem['display']
                )
            );
            $this->formTokenHelper->generateFormToken();

            return [
                'mode' => $this->fetchMenuItemModes($menuItem['mode']),
                'modules' => $this->fetchModules($menuItem),
                'target' => $this->get('core.helpers.forms')->linkTargetSelectGenerator('target', $menuItem['target']),
                'SEO_FORM_FIELDS' => $this->seo->formFields($menuItem['uri']),
                'form' => array_merge($menuItem, $this->request->getPost()->all())
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param array $menuItem
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $menuItem, $id)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData, $menuItem, $id) {
                $this->menuItemFormValidation->validate($formData);

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
                    Menus\Model\MenuItemRepository::TABLE_NAME,
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

                return $this->redirectMessages()->setMessage($bool,
                    $this->translator->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
            },
            'acp/menus'
        );
    }
}
