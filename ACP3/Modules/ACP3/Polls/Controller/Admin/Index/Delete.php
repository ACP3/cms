<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Polls\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository
     */
    protected $pollRepository;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext    $context
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository $pollRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Polls\Model\Repository\PollRepository $pollRepository
    ) {
        parent::__construct($context);

        $this->pollRepository = $pollRepository;
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
            $this,
            $action,
            function ($items) {
                $bool = false;
                foreach ($items as $item) {
                    $bool = $this->pollRepository->delete($item);
                }

                Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

                return $bool !== false;
            }
        );
    }
}
