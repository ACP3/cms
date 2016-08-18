<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Guestbook;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var Guestbook\Model\GuestbookModel
     */
    protected $guestbookModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Guestbook\Model\GuestbookModel $guestbookModel
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Guestbook\Model\GuestbookModel $guestbookModel
    ) {
        parent::__construct($context);

        $this->guestbookModel = $guestbookModel;
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
                return $this->guestbookModel->delete($items);
            }
        );
    }
}
