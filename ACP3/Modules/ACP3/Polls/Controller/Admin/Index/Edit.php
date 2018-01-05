<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

class Edit extends AbstractFormAction
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation
     */
    protected $pollsValidator;
    /**
     * @var Polls\Model\PollsModel
     */
    protected $pollsModel;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param Polls\Model\PollsModel $pollsModel
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository $answerRepository
     * @param \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation $pollsValidator
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Polls\Model\PollsModel $pollsModel,
        Polls\Model\Repository\AnswerRepository $answerRepository,
        Polls\Validation\AdminFormValidation $pollsValidator
    ) {
        parent::__construct($context, $formsHelper, $answerRepository);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->pollsModel = $pollsModel;
        $this->pollsValidator = $pollsValidator;
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
            $this->title->setPageTitlePrefix($poll['title']);

            return [
                'answers' => $this->getAnswers($id),
                'options' => $this->fetchOptions($poll['multiple']),
                'form' => \array_merge($poll, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ];
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

    /**
     * @param int $pollId
     * @return array
     */
    protected function getAnswers($pollId)
    {
        if ($this->request->getPost()->has('add_answer')) {
            $answers = $this->addNewAnswer($this->request->getPost()->get('answers', []));
        } else {
            $answers = $this->answerRepository->getAnswersWithVotesByPollId($pollId);
        }

        return $answers;
    }

    /**
     * @inheritdoc
     */
    protected function fetchOptions($useMultipleChoice)
    {
        $reset = [
            '1' => $this->translator->t('polls', 'reset_votes'),
        ];

        return \array_merge(
            parent::fetchOptions($useMultipleChoice),
            $this->formsHelper->checkboxGenerator('reset', $reset, '0')
        );
    }
}
