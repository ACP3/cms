<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Details;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

class Edit extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var Comments\Model\CommentsModel
     */
    protected $commentsModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Comments\Model\CommentsModel $commentsModel,
        Comments\Validation\AdminFormValidation $adminFormValidation,
        Core\Helpers\FormToken $formTokenHelper
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->formTokenHelper = $formTokenHelper;
        $this->commentsModel = $commentsModel;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        $comment = $this->commentsModel->getOneById($id);

        if (empty($comment) === false) {
            $this->breadcrumb
                ->append(
                    $this->translator->t($comment['module'], $comment['module']),
                    'acp/comments/details/index/id_' . $comment['module_id']
                )
                ->append($this->translator->t('comments', 'admin_details_edit'));

            $this->title->setPageTitlePrefix($comment['name']);

            return [
                'form' => \array_merge($comment, $this->request->getPost()->all()),
                'module_id' => (int) $comment['module_id'],
                'form_token' => $this->formTokenHelper->renderFormToken(),
                'can_use_emoticons' => true,
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost(int $id)
    {
        $comment = $this->commentsModel->getOneById($id);

        return $this->actionHelper->handleSaveAction(
            function () use ($id, $comment) {
                $formData = $this->request->getPost()->all();
                $this->adminFormValidation->validate($formData);

                $updateValues = [
                    'message' => $formData['message'],
                ];
                if ($comment['user_id'] === null && !empty($formData['name'])) {
                    $updateValues['name'] = $formData['name'];
                }

                return $this->commentsModel->save($updateValues, $id);
            },
            'acp/comments/details/index/id_' . $comment['module_id']
        );
    }
}
