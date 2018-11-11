<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

class Delete extends Core\Controller\AbstractFormAction
{
    /**
     * @var Articles\Model\ArticlesModel
     */
    protected $articlesModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Articles\Model\ArticlesModel $articlesModel
    ) {
        parent::__construct($context);

        $this->articlesModel = $articlesModel;
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
                return $this->articlesModel->delete($items);
            }
        );
    }
}
