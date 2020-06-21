<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\News;

class Edit extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var News\Model\NewsModel
     */
    private $newsModel;
    /**
     * @var \ACP3\Modules\ACP3\News\ViewProviders\AdminNewsEditViewProvider
     */
    private $adminNewsEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        News\Model\NewsModel $newsModel,
        News\ViewProviders\AdminNewsEditViewProvider $adminNewsEditViewProvider
    ) {
        parent::__construct($context);

        $this->newsModel = $newsModel;
        $this->adminNewsEditViewProvider = $adminNewsEditViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id): array
    {
        $news = $this->newsModel->getOneById($id);

        if (empty($news) === false) {
            return ($this->adminNewsEditViewProvider)($news);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
