<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

class Edit extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation
     */
    protected $pollsValidator;
    /**
     * @var Polls\Model\PollsModel
     */
    protected $pollsModel;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param Polls\Model\PollsModel $pollsModel
     * @param \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation $pollsValidator
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Polls\Model\PollsModel $pollsModel,
        Polls\Validation\AdminFormValidation $pollsValidator
    ) {
        parent::__construct($context);

        $this->pollsModel = $pollsModel;
        $this->pollsValidator = $pollsValidator;
        $this->block = $block;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $poll = $this->pollsModel->getOneById($id);

        if (empty($poll) === false) {
            return $this->block
                ->setRequestData($this->request->getPost()->all())
                ->setData($poll)
                ->render();
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost($id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->pollsValidator->validate($formData);

            $formData['user_id'] = $this->user->getUserId();
            $bool = $this->pollsModel->save($formData, $id);

            if (!empty($formData['reset'])) {
                $this->pollsModel->resetVotesByPollId($id);
            }

            $bool2 = $this->pollsModel->saveAnswers($formData['answers'], $id);

            return $bool !== false && $bool2 !== false;
        });
    }
}
