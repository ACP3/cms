<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Files\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var Files\Model\FilesModel
     */
    private $filesModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Files\Model\FilesModel $filesModel
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Files\Model\FilesModel $filesModel
    ) {
        parent::__construct($context);

        $this->filesModel = $filesModel;
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
                return $this->filesModel->delete($items);
            }
        );
    }
}
