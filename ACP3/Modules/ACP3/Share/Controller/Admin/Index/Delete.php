<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Share;

class Delete extends Core\Controller\AbstractFormAction
{
    /**
     * @var Share\Model\ShareModel
     */
    protected $shareModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Share\Model\ShareModel $shareModel
    ) {
        parent::__construct($context);

        $this->shareModel = $shareModel;
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
                $result = $this->shareModel->delete($items);

                return $result;
            }
        );
    }
}
