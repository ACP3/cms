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
 * Class Edit
 * @package ACP3\Modules\ACP3\Comments\Controller\Admin\Details
 */
class Edit extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\CommentRepository
     */
    protected $commentRepository;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\System\Model\ModuleRepository
     */
    protected $systemModuleRepository;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelpers;

    /**
     * Details constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext                 $context
     * @param \ACP3\Modules\ACP3\Comments\Model\CommentRepository        $commentRepository
     * @param \ACP3\Modules\ACP3\Comments\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\System\Model\ModuleRepository           $systemModuleRepository
     * @param \ACP3\Core\Helpers\FormToken                               $formTokenHelper
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Comments\Model\CommentRepository $commentRepository,
        Comments\Validation\AdminFormValidation $adminFormValidation,
        System\Model\ModuleRepository $systemModuleRepository,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->commentRepository = $commentRepository;
        $this->adminFormValidation = $adminFormValidation;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Emoticons\Helpers $emoticonsHelpers
     *
     * @return $this
     */
    public function setEmoticonsHelpers(Emoticons\Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;

        return $this;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        $comment = $this->commentRepository->getOneById($id);

        if (empty($comment) === false) {
            $this->breadcrumb
                ->append(
                    $this->translator->t($comment['module'], $comment['module']),
                    'acp/comments/details/index/id_' . $comment['module_id']
                )
                ->append($this->translator->t('comments', 'admin_details_edit'))
                ->setTitlePostfix($comment['name']);

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost(
                    $this->request->getPost()->all(),
                    $comment,
                    $id,
                    $comment['module_id']
                );
            }

            if ($this->emoticonsHelpers) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', $this->emoticonsHelpers->emoticonsList());
            }

            return [
                'form' => array_merge($comment, $this->request->getPost()->all()),
                'module_id' => (int)$comment['module_id'],
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }
    /**
     * @param array $formData
     * @param array $comment
     * @param int   $id
     * @param int   $moduleId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $comment, $id, $moduleId)
    {
        return $this->actionHelper->handleEditPostAction(
            function () use ($formData, $comment, $id) {
                $this->adminFormValidation->validate($formData);

                $updateValues = [];
                $updateValues['message'] = Core\Functions::strEncode($formData['message']);
                if ((empty($comment['user_id']) || $this->validator->is(Core\Validation\ValidationRules\IntegerValidationRule::class, $comment['user_id']) === false) &&
                    !empty($formData['name'])
                ) {
                    $updateValues['name'] = Core\Functions::strEncode($formData['name']);
                }

                $bool = $this->commentRepository->update($updateValues, $id);

                $this->formTokenHelper->unsetFormToken();

                return $bool;
            },
            'acp/comments/details/index/id_' . $moduleId
        );
    }
}
