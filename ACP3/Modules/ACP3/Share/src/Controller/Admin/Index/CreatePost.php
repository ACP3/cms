<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Share\Model\ShareModel;
use ACP3\Modules\ACP3\Share\Validation\AdminFormValidation;

class CreatePost extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
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
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        ShareModel $shareModel,
        AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->shareModel = $shareModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminFormValidation->validate($formData);

            return $this->shareModel->save($formData);
        });
    }
}
