<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Share\Model\ShareModel;
use ACP3\Modules\ACP3\Share\Validation\AdminFormValidation;
use ACP3\Modules\ACP3\Share\ViewProviders\AdminShareEditViewProvider;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Share\Validation\AdminFormValidation
     */
    private $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\ShareModel
     */
    private $shareModel;
    /**
     * @var \ACP3\Modules\ACP3\Share\ViewProviders\AdminShareEditViewProvider
     */
    private $adminShareEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        ShareModel $shareModel,
        AdminFormValidation $adminFormValidation,
        AdminShareEditViewProvider $adminShareEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->shareModel = $shareModel;
        $this->adminShareEditViewProvider = $adminShareEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): array
    {
        $defaults = [
            'uri' => '',
        ];

        return ($this->adminShareEditViewProvider)($defaults);
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

            $this->adminFormValidation->validate($formData);

            return $this->shareModel->save($formData);
        });
    }
}
