<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Files\Model\FilesModel;

class Duplicate extends AbstractWidgetAction
{
    /**
     * @var FilesModel
     */
    private $filesModel;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        WidgetContext $context,
        Action $actionHelper,
        FilesModel $filesModel
    ) {
        parent::__construct($context);

        $this->filesModel = $filesModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute(int $id)
    {
        return $this->actionHelper->handleDuplicateAction(function () use ($id) {
            return $this->filesModel->duplicate($id);
        });
    }
}
