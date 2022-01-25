<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Comments\Model\CommentByModuleModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Delete extends AbstractWidgetAction
{
    public function __construct(
        WidgetContext $context,
        private FormAction $actionHelper,
        private CommentByModuleModel $commentByModuleModel
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|JsonResponse|RedirectResponse
     */
    public function __invoke(?string $action = null): array|JsonResponse|RedirectResponse
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            fn (array $items) => (bool) $this->commentByModuleModel->delete($items)
        );
    }
}
