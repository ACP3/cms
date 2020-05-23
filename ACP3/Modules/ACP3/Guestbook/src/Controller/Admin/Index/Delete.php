<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Guestbook;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Guestbook\Model\GuestbookModel
     */
    protected $guestbookModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Guestbook\Model\GuestbookModel $guestbookModel
    ) {
        parent::__construct($context);

        $this->guestbookModel = $guestbookModel;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(?string $action = null)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->guestbookModel->delete($items);
            }
        );
    }
}
