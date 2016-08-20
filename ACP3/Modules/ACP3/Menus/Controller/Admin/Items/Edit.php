<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Items
 */
class Edit extends AbstractFormAction
{
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
     * @var \ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields
     */
    protected $menuItemFormFieldsHelper;
    /**
     * @var Menus\Model\MenuItemsModel
     */
    protected $menuItemsModel;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param Menus\Model\MenuItemsModel $menuItemsModel
     * @param \ACP3\Modules\ACP3\Menus\Cache $menusCache
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields $menuItemFormFieldsHelper
     * @param \ACP3\Modules\ACP3\Menus\Validation\MenuItemFormValidation $menuItemFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Menus\Model\MenuItemsModel $menuItemsModel,
        Menus\Cache $menusCache,
        Menus\Helpers\MenuItemFormFields $menuItemFormFieldsHelper,
        Menus\Validation\MenuItemFormValidation $menuItemFormValidation
    ) {
        parent::__construct($context, $formsHelper);

        $this->formTokenHelper = $formTokenHelper;
        $this->menusCache = $menusCache;
        $this->menuItemFormFieldsHelper = $menuItemFormFieldsHelper;
        $this->menuItemFormValidation = $menuItemFormValidation;
        $this->menuItemsModel = $menuItemsModel;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $menuItem = $this->menuItemsModel->getOneById($id);

        if (empty($menuItem) === false) {
            $this->title->setPageTitlePostfix($menuItem['title']);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $menuItem, $id);
            }

            if ($this->articlesHelpers) {
                $matches = [];
                if (count($this->request->getPost()->all()) == 0 && $menuItem['mode'] == 4) {
                    preg_match_all(Menus\Helpers\MenuItemsList::ARTICLES_URL_KEY_REGEX, $menuItem['uri'], $matches);
                }

                $this->view->assign('articles',
                    $this->articlesHelpers->articlesList(!empty($matches[2]) ? $matches[2][0] : '')
                );
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

            return [
                'mode' => $this->fetchMenuItemTypes($menuItem['mode']),
                'modules' => $this->fetchModules($menuItem),
                'target' => $this->formsHelper->linkTargetChoicesGenerator('target', $menuItem['target']),
                'form' => array_merge($menuItem, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param array $menuItem
     * @param int   $menuItemId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $menuItem, $menuItemId)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData, $menuItem, $menuItemId) {
                $this->menuItemFormValidation->validate($formData);

                $formData['mode'] = $this->fetchMenuItemModeForSave($formData);
                $formData['uri'] = $this->fetchMenuItemUriForSave($formData);
                $result = $this->menuItemsModel->saveMenuItem($formData, $menuItemId);

                $this->menusCache->saveMenusCache();

                return $this->redirectMessages()->setMessage(
                    $result,
                    $this->translator->t('system', $result !== false ? 'edit_success' : 'edit_error'),
                    'acp/menus'
                );
            },
            'acp/menus'
        );
    }
}
