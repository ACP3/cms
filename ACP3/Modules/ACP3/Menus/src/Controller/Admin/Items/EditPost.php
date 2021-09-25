<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Menus;

class EditPost extends AbstractFormAction
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
     * @var \ACP3\Core\Helpers\FormAction
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        FormAction $actionHelper,
        Menus\Model\MenuItemsModel $menuItemsModel,
        Menus\Validation\MenuItemFormValidation $menuItemFormValidation
    ) {
        parent::__construct($context);

        $this->menuItemFormValidation = $menuItemFormValidation;
        $this->menuItemsModel = $menuItemsModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id)
    {
        return $this->actionHelper->handleSaveAction(
            function () use ($id) {
                $formData = $this->request->getPost()->all();

                $this->menuItemFormValidation->validate($formData);

                $formData['uri'] = $this->fetchMenuItemUriForSave($formData);

                return $this->menuItemsModel->save($formData, $id);
            },
            'acp/menus'
        );
    }
}
