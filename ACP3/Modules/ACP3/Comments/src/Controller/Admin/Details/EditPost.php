<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Details;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Comments;

class EditPost extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validation\AdminFormValidation
     */
    private $adminFormValidation;
    /**
     * @var Comments\Model\CommentsModel
     */
    private $commentsModel;
    /**
     * @var \ACP3\Core\Helpers\FormAction
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        FormAction $actionHelper,
        Comments\Model\CommentsModel $commentsModel,
        Comments\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->commentsModel = $commentsModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id)
    {
        $comment = $this->commentsModel->getOneById($id);

        return $this->actionHelper->handleSaveAction(
            function () use ($id, $comment) {
                $formData = $this->request->getPost()->all();

                $formData = array_merge($comment, $formData);

                $this->adminFormValidation->validate($formData);

                return $this->commentsModel->save($formData, $id);
            },
            'acp/comments/details/index/id_' . $comment['module_id']
        );
    }
}
