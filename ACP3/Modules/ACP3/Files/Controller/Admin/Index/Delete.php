<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;

class Delete extends Core\Controller\AbstractFormAction
{
    /**
     * @var Files\Model\FilesModel
     */
    private $filesModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Files\Model\FilesModel $filesModel
    ) {
        parent::__construct($context);

        $this->filesModel = $filesModel;
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
                return $this->filesModel->delete($items);
            }
        );
    }
}
