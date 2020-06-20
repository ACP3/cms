<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Menus;

class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Validation\MenuItemFormValidation
     */
    private $menuItemFormValidation;
    /**
     * @var Menus\Model\MenuItemsModel
     */
    private $menuItemsModel;
    /**
     * @var \ACP3\Modules\ACP3\Menus\ViewProviders\AdminMenuItemEditViewProvider
     */
    private $adminMenuItemEditViewProvider;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        Menus\Model\MenuItemsModel $menuItemsModel,
        Menus\Validation\MenuItemFormValidation $menuItemFormValidation,
        Menus\ViewProviders\AdminMenuItemEditViewProvider $adminMenuItemEditViewProvider
    ) {
        parent::__construct($context);

        $this->menuItemFormValidation = $menuItemFormValidation;
        $this->menuItemsModel = $menuItemsModel;
        $this->adminMenuItemEditViewProvider = $adminMenuItemEditViewProvider;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): array
    {
        $defaults = [
            'title' => '',
            'uri' => '',
            'mode' => '',
            'target' => null,
            'block_id' => 0,
            'parent_id' => 0,
            'left_id' => 0,
            'right_id' => 0,
            'display' => 1,
        ];

        return ($this->adminMenuItemEditViewProvider)($defaults);
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->menuItemFormValidation->validate($formData);

                $formData['mode'] = $this->fetchMenuItemModeForSave($formData);
                $formData['uri'] = $this->fetchMenuItemUriForSave($formData);

                return $this->menuItemsModel->save($formData);
            },
            'acp/menus'
        );
    }
}
