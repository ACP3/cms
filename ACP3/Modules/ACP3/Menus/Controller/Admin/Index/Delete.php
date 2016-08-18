<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var Menus\Model\MenusModel
     */
    protected $menusModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Menus\Model\MenusModel $menusModel
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Menus\Model\MenusModel $menusModel
    ) {
        parent::__construct($context);

        $this->menusModel = $menusModel;
    }

    /**
     * @param string $action
     *
     * @return mixed
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->menusModel->delete($items);
            }
        );
    }
}
