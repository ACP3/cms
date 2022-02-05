<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Share;
use Symfony\Component\HttpFoundation\Response;

class Delete extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\Context $context,
        private FormAction $actionHelper,
        private Share\Model\ShareModel $shareModel
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|Response
     */
    public function __invoke(?string $action = null): array|Response
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            fn (array $items) => $this->shareModel->delete($items)
        );
    }
}
