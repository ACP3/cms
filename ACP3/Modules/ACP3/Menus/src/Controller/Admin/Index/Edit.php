<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

class Edit extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Menus\Model\MenusModel
     */
    private $menusModel;
    /**
     * @var \ACP3\Modules\ACP3\Menus\ViewProviders\AdminMenuEditViewProvider
     */
    private $adminMenuEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Menus\Model\MenusModel $menusModel,
        Menus\ViewProviders\AdminMenuEditViewProvider $adminMenuEditViewProvider
    ) {
        parent::__construct($context);

        $this->menusModel = $menusModel;
        $this->adminMenuEditViewProvider = $adminMenuEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id): array
    {
        $menu = $this->menusModel->getOneById($id);

        if (empty($menu) === false) {
            return ($this->adminMenuEditViewProvider)($menu);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
