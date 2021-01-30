<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Newsletter;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Newsletter\Model\NewsletterModel
     */
    private $newsletterModel;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Newsletter\Model\NewsletterModel $newsletterModel
    ) {
        parent::__construct($context);

        $this->newsletterModel = $newsletterModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(?string $action = null)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->newsletterModel->delete($items);
            }
        );
    }
}
