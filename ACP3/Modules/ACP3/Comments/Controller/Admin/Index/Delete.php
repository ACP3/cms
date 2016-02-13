<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Comments\Controller\Admin\Index
 */
class Delete extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\CommentRepository
     */
    protected $commentRepository;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext          $context
     * @param \ACP3\Modules\ACP3\Comments\Model\CommentRepository $commentRepository
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Comments\Model\CommentRepository $commentRepository)
    {
        parent::__construct($context);

        $this->commentRepository = $commentRepository;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;
                foreach ($items as $item) {
                    $bool = $this->commentRepository->delete($item, 'module_id');
                }

                return $bool;
            }
        );
    }
}
