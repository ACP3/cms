<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Comments;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\CommentByModuleModel
     */
    private $commentByModuleModel;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Comments\Model\CommentByModuleModel $commentByModuleModel
    ) {
        parent::__construct($context);

        $this->actionHelper = $actionHelper;
        $this->commentByModuleModel = $commentByModuleModel;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(?string $action = null)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return (bool) $this->commentByModuleModel->delete($items);
            }
        );
    }
}
