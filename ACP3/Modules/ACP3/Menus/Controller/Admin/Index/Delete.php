<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

class Delete extends Core\Controller\AbstractFormAction
{
    /**
     * @var Menus\Model\MenusModel
     */
    protected $menusModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Menus\Model\MenusModel $menusModel
    ) {
        parent::__construct($context);

        $this->menusModel = $menusModel;
    }

    /**
     * @param string $action
     *
     * @return mixed
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(string $action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->menusModel->delete($items);
            }
        );
    }
}
