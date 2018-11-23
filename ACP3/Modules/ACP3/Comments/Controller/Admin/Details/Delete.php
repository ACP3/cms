<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Details;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

class Delete extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\Repository\CommentRepository
     */
    protected $commentRepository;
    /**
     * @var Comments\Model\CommentsModel
     */
    protected $commentsModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Comments\Model\CommentsModel $commentsModel,
        Comments\Model\Repository\CommentRepository $commentRepository
    ) {
        parent::__construct($context);

        $this->commentRepository = $commentRepository;
        $this->commentsModel = $commentsModel;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(int $id, string $action = '')
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $action,
            function (array $items) use ($id) {
                $result = $this->commentsModel->delete($items);

                // If there are no comments for the given module, redirect to the general comments admin panel page
                if ($this->commentRepository->countAll($id) === 0) {
                    $redirectUrl = 'acp/comments';
                } else {
                    $redirectUrl = 'acp/comments/details/index/id_' . $id;
                }

                return $this->redirectMessages()->setMessage(
                    $result,
                    $this->translator->t('system', $result !== false ? 'delete_success' : 'delete_error'),
                    $redirectUrl
                );
            },
            'acp/comments/details/delete/id_' . $id,
            'acp/comments/details/index/id_' . $id
        );
    }
}
