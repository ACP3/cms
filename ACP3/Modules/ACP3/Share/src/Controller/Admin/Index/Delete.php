<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Share;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Share\Model\ShareModel
     */
    private $shareModel;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Share\Model\ShareModel $shareModel
    ) {
        parent::__construct($context);

        $this->shareModel = $shareModel;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(?string $action = null)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->shareModel->delete($items);
            }
        );
    }
}
