<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Validation\MenuFormValidation
     */
    private $menuFormValidation;
    /**
     * @var Menus\Model\MenusModel
     */
    private $menusModel;
    /**
     * @var \ACP3\Modules\ACP3\Menus\ViewProviders\AdminMenuEditViewProvider
     */
    private $adminMenuEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Menus\Model\MenusModel $menusModel,
        Menus\Validation\MenuFormValidation $menuFormValidation,
        Menus\ViewProviders\AdminMenuEditViewProvider $adminMenuEditViewProvider
    ) {
        parent::__construct($context);

        $this->menusModel = $menusModel;
        $this->menuFormValidation = $menuFormValidation;
        $this->adminMenuEditViewProvider = $adminMenuEditViewProvider;
    }

    public function execute(): array
    {
        return ($this->adminMenuEditViewProvider)(['index_name' => '', 'title' => '']);
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->menuFormValidation->validate($formData);

            return $this->menusModel->save($formData);
        });
    }
}
