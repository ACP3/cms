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
     * @var \ACP3\Modules\ACP3\Guestbook\Model\Repository\GuestbookRepository
     */
    protected $guestbookRepository;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext             $context
     * @param \ACP3\Modules\ACP3\Guestbook\Model\Repository\GuestbookRepository $guestbookRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Guestbook\Model\Repository\GuestbookRepository $guestbookRepository
    ) {
        parent::__construct($context);

        $this->guestbookRepository = $guestbookRepository;
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
            $action, function (array $items) {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->guestbookRepository->delete($item);
            }

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $bool;
        }
        );
    }
}
