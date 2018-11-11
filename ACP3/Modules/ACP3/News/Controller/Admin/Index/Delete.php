<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\News;

class Delete extends Core\Controller\AbstractFormAction
{
    /**
     * @var News\Model\NewsModel
     */
    protected $newsModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        News\Model\NewsModel $newsModel
    ) {
        parent::__construct($context);

        $this->newsModel = $newsModel;
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
                return $this->newsModel->delete($items);
            }
        );
    }
}
