<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Menus;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class EditPost extends AbstractFormAction
{
    public function __construct(
        Core\Controller\Context\Context $context,
        private FormAction $actionHelper,
        private Menus\Model\MenuItemsModel $menuItemsModel,
        private Menus\Validation\MenuItemFormValidation $menuItemFormValidation
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|Response
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(int $id): array|string|Response
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
