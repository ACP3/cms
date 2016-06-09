<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
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
     * @var \ACP3\Modules\ACP3\Seo\Core\Router\Aliases
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
     * @param \ACP3\Modules\ACP3\Seo\Core\Router\Aliases                 $aliases
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
        \ACP3\Modules\ACP3\Seo\Core\Router\Aliases $aliases,
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
        if ($this->request->getPost()->count() !== 0) {
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
            'target' => $this->formsHelper->linkTargetChoicesGenerator('target'),
            'SEO_FORM_FIELDS' => $this->metaFormFieldsHelper ? $this->metaFormFieldsHelper->formFields() : [],
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

                if ($this->metaStatementsHelper) {
                    $this->updateSeoInformation($formData);
                }

                $this->menusCache->saveMenusCache();

                return $this->redirectMessages()->setMessage(
                    $bool,
                    $this->translator->t('system', $bool !== false ? 'create_success' : 'create_error'),
                    'acp/menus'
                );
            },
            'acp/menus'
        );
    }

    /**
     * @param array $formData
     */
    protected function updateSeoInformation(array $formData)
    {
        if ($formData['mode'] != 3) {
            $path = $formData['mode'] == 1 ? $formData['module'] : $formData['uri'];
            if ($this->aliases->uriAliasExists($formData['uri'])) {
                $alias = !empty($formData['alias']) ? $formData['alias'] : $this->aliases->getUriAlias($formData['uri']);
                $keywords = $this->metaStatementsHelper->getKeywords($formData['uri']);
                $description = $this->metaStatementsHelper->getDescription($formData['uri']);
            } else {
                $alias = $formData['alias'];
                $keywords = $formData['seo_keywords'];
                $description = $formData['seo_description'];
            }

            $this->insertUriAlias(
                [
                    'alias' => $formData['mode'] == 1 ? '' : $alias,
                    'seo_keywords' => $keywords,
                    'seo_description' => $description,
                    'seo_robots' => (int)$formData['seo_robots']
                ],
                $path
            );
        }
    }
}
