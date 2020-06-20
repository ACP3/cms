<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Articles\Model\ArticlesModel;

class Duplicate extends AbstractFrontendAction
{
    /**
     * @var ArticlesModel
     */
    private $articlesModel;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        FrontendContext $context,
        Action $actionHelper,
        ArticlesModel $articlesModel
    ) {
        parent::__construct($context);

        $this->articlesModel = $articlesModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        return $this->actionHelper->handleDuplicateAction(function () use ($id) {
            return $this->articlesModel->duplicate($id);
        });
    }
}
