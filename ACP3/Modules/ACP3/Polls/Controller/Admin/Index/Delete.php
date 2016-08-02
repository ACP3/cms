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
     * @var Polls\Model\PollsModel
     */
    protected $pollsModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Polls\Model\PollsModel $pollsModel
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Polls\Model\PollsModel $pollsModel
    ) {
        parent::__construct($context);

        $this->pollsModel = $pollsModel;
    }

    /**
     * @param string $action
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->pollsModel->delete($items);
            }
        );
    }
}
