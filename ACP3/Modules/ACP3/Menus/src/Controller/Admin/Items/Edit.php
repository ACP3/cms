<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

class Edit extends AbstractFormAction
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

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Menus\Model\MenuItemsModel $menuItemsModel,
        Menus\Validation\MenuItemFormValidation $menuItemFormValidation,
        Menus\ViewProviders\AdminMenuItemEditViewProvider $adminMenuItemEditViewProvider
    ) {
        parent::__construct($context);

        $this->menuItemFormValidation = $menuItemFormValidation;
        $this->menuItemsModel = $menuItemsModel;
        $this->adminMenuItemEditViewProvider = $adminMenuItemEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        $menuItem = $this->menuItemsModel->getOneById($id);

        if (empty($menuItem) === false) {
            return ($this->adminMenuItemEditViewProvider)($menuItem);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(
            function () use ($id) {
                $formData = $this->request->getPost()->all();

                $this->menuItemFormValidation->validate($formData);

                $formData['mode'] = $this->fetchMenuItemModeForSave($formData);
                $formData['uri'] = $this->fetchMenuItemUriForSave($formData);

                return $this->menuItemsModel->save($formData, $id);
            },
            'acp/menus'
        );
    }
}
