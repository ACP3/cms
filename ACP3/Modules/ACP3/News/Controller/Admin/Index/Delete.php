<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\News;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\News\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var News\Model\NewsModel
     */
    protected $newsModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param News\Model\NewsModel $newsModel
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        News\Model\NewsModel $newsModel
    ) {
        parent::__construct($context);

        $this->newsModel = $newsModel;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->newsModel->delete($items);
            }
        );
    }
}
