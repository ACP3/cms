<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\News\Model\NewsModel;

class Duplicate extends AbstractWidgetAction
{
    /**
     * @var NewsModel
     */
    private $newsModel;
    /**
     * @var \ACP3\Core\Helpers\FormAction
     */
    private $actionHelper;

    public function __construct(
        WidgetContext $context,
        FormAction $actionHelper,
        NewsModel $newsModel
    ) {
        parent::__construct($context);

        $this->newsModel = $newsModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id)
    {
        return $this->actionHelper->handleDuplicateAction(function () use ($id) {
            return $this->newsModel->duplicate($id);
        });
    }
}
