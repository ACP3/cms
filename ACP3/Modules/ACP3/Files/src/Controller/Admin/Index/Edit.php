<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;

class Edit extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Files\Model\FilesModel
     */
    private $filesModel;
    /**
     * @var \ACP3\Modules\ACP3\Files\ViewProviders\AdminFileEditViewProvider
     */
    private $adminFileEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Files\Model\FilesModel $filesModel,
        Files\ViewProviders\AdminFileEditViewProvider $adminFileEditViewProvider
    ) {
        parent::__construct($context);

        $this->filesModel = $filesModel;
        $this->adminFileEditViewProvider = $adminFileEditViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id): array
    {
        $file = $this->filesModel->getOneById($id);

        if (empty($file) === false) {
            return ($this->adminFileEditViewProvider)($file);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
