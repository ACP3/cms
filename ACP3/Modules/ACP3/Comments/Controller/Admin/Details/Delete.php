<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Details;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\System;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Comments\Controller\Admin\Details
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
     * @param int    $id
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id, $action = '')
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $this,
            $action,
            function ($items) use ($id) {
                $bool = false;

                foreach ($items as $item) {
                    $bool = $this->commentRepository->delete($item);
                }

                // If there are no comments for the given module, redirect to the general comments admin panel page
                if ($this->commentRepository->countAll($id) == 0) {
                    return $this->redirectMessages()->setMessage(
                        $bool,
                        $this->translator->t('system', $bool !== false ? 'delete_success' : 'delete_error'),
                        'acp/comments'
                    );
                }

                return $this->redirectMessages()->setMessage(
                    $bool,
                    $this->translator->t('system', $bool !== false ? 'delete_success' : 'delete_error'),
                    'acp/comments/details/index/id_' . $id
                );
            },
            'acp/comments/details/delete/id_' . $id,
            'acp/comments/details/index/id_' . $id
        );
    }
}
