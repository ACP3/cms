<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Items
 */
class Create extends AbstractFormAction
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
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuRepository
     */
    protected $menuRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList
     */
    protected $menusHelpers;
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
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                 $context
     * @param \ACP3\Core\Router\Aliases                                  $aliases
     * @param \ACP3\Core\NestedSet                                       $nestedSet
     * @param \ACP3\Core\Helpers\Forms                                   $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuRepository              $menuRepository
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository          $menuItemRepository
     * @param \ACP3\Modules\ACP3\Menus\Cache                             $menusCache
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields        $menuItemFormFieldsHelper
     * @param \ACP3\Modules\ACP3\Menus\Validation\MenuItemFormValidation $menuItemFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Router\Aliases $aliases,
        Core\NestedSet $nestedSet,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Menus\Model\MenuRepository $menuRepository,
        Menus\Model\MenuItemRepository $menuItemRepository,
        Menus\Cache $menusCache,
        Menus\Helpers\MenuItemFormFields $menuItemFormFieldsHelper,
        Menus\Validation\MenuItemFormValidation $menuItemFormValidation
    ) {
        parent::__construct($context, $formsHelper);

        $this->aliases = $aliases;
        $this->nestedSet = $nestedSet;
        $this->formTokenHelper = $formTokenHelper;
        $this->menuRepository = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
        $this->menuItemFormFieldsHelper = $menuItemFormFieldsHelper;
        $this->menuItemFormValidation = $menuItemFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all());
        }

        if ($this->articlesHelpers) {
            $this->view->assign('articles', $this->articlesHelpers->articlesList());
        }

        $defaults = [
            'title' => '',
            'uri' => '',
        ];

        $this->view->assign($this->menuItemFormFieldsHelper->createMenuItemFormFields());

        return [
            'mode' => $this->fetchMenuItemTypes(),
            'modules' => $this->fetchModules(),
            'target' => $this->formsHelper->linkTargetSelectGenerator('target'),
            'SEO_FORM_FIELDS' => $this->seo->formFields(),
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $this->menuItemFormValidation->validate($formData);

                $insertValues = [
                    'id' => '',
                    'mode' => $this->fetchMenuItemModeForSave($formData),
                    'block_id' => (int)$formData['block_id'],
                    'parent_id' => (int)$formData['parent_id'],
                    'display' => $formData['display'],
                    'title' => $this->get('core.helpers.secure')->strEncode($formData['title']),
                    'uri' => $this->fetchMenuItemUriForSave($formData),
                    'target' => $formData['display'] == 0 ? 1 : $formData['target'],
                ];

                $bool = $this->nestedSet->insertNode(
                    (int)$formData['parent_id'],
                    $insertValues,
                    Menus\Model\MenuItemRepository::TABLE_NAME,
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

                return $this->redirectMessages()->setMessage($bool,
                    $this->translator->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
            },
            'acp/menus'
        );
    }
}
