<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\Context;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Articles\Model\ArticlesModel;

class Duplicate extends AbstractWidgetAction
{
    public function __construct(
        Context $context,
        private FormAction $actionHelper,
        private ArticlesModel $articlesModel
    ) {
        parent::__construct($context);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id): \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->actionHelper->handleDuplicateAction(fn () => $this->articlesModel->duplicate($id));
    }
}
