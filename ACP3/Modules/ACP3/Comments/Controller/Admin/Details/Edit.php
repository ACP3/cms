<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Details;

use ACP3\Core;
use ACP3\Core\Validation\ValidationRules\IntegerValidationRule;
use ACP3\Modules\ACP3\Comments;

class Edit extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var Comments\Model\CommentsModel
     */
    protected $commentsModel;
    /**
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;
    /**
     * @var Core\Validation\Validator
     */
    private $validator;

    /**
     * Details constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface $block
     * @param Core\Validation\Validator $validator
     * @param Comments\Model\CommentsModel $commentsModel
     * @param \ACP3\Modules\ACP3\Comments\Validation\AdminFormValidation $adminFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        Core\Validation\Validator $validator,
        Comments\Model\CommentsModel $commentsModel,
        Comments\Validation\AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->adminFormValidation = $adminFormValidation;
        $this->commentsModel = $commentsModel;
        $this->block = $block;
        $this->validator = $validator;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function execute(int $id)
    {
        return $this->block
            ->setDataById($id)
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
                if ((empty($comment['user_id']) || $this->validator->is(IntegerValidationRule::class, $comment['user_id']) === false) &&
                    !empty($formData['name'])
                ) {
                    $updateValues['name'] = $formData['name'];
                }

                return $this->commentsModel->save($updateValues, $id);
            },
            'acp/comments/details/index/id_' . $comment['module_id']
        );
    }
}
