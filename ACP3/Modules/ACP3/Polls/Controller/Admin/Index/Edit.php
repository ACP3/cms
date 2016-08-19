<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Polls\Controller\Admin\Index
 */
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
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param Polls\Model\PollsModel $pollsModel
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository $answerRepository
     * @param \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation $pollsValidator
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
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
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $poll = $this->pollsModel->getOneById($id);

        if (empty($poll) === false) {
            $this->title->setPageTitlePostfix($poll['title']);

            if ($this->request->getPost()->has('submit')) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            return [
                'answers' => $this->getAnswers($id),
                'options' => $this->fetchOptions($poll['multiple']),
                'form' => array_merge($poll, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param int   $pollId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $pollId)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $pollId) {
            $this->pollsValidator->validate($formData);

            $bool = $this->pollsModel->savePoll($formData, $this->user->getUserId(), $pollId);

            if (!empty($formData['reset'])) {
                $this->pollsModel->resetVotesByPollId($pollId);
            }

            $bool2 = $this->pollsModel->saveAnswers($formData['answers'], $pollId);

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
        $options = parent::fetchOptions($useMultipleChoice);
        $options[] = [
            'name' => 'reset',
            'checked' => $this->formsHelper->selectEntry('reset', '1', '0', 'checked'),
            'lang' => $this->translator->t('polls', 'reset_votes')
        ];

        return $options;
    }
}
