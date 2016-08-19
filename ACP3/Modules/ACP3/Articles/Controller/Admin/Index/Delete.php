<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var Articles\Model\ArticlesModel
     */
    protected $articlesModel;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Articles\Model\ArticlesModel $articlesModel
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Articles\Model\ArticlesModel $articlesModel
    ) {
        parent::__construct($context);

        $this->articlesModel = $articlesModel;
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
                return $this->articlesModel->delete($items);
            }
        );
    }
}
