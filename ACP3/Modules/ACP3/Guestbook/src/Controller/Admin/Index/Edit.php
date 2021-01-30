<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Guestbook;

class Edit extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Guestbook\Model\GuestbookModel
     */
    private $guestbookModel;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\ViewProviders\AdminGuestbookEditViewProvider
     */
    private $adminGuestbookEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Guestbook\Model\GuestbookModel $guestbookModel,
        Guestbook\ViewProviders\AdminGuestbookEditViewProvider $adminGuestbookEditViewProvider
    ) {
        parent::__construct($context);

        $this->guestbookModel = $guestbookModel;
        $this->adminGuestbookEditViewProvider = $adminGuestbookEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id): array
    {
        $guestbookEntry = $this->guestbookModel->getOneById($id);
        if (empty($guestbookEntry) === false) {
            return ($this->adminGuestbookEditViewProvider)($guestbookEntry);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
