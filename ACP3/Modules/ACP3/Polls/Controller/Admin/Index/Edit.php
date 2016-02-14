<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
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
     * @var \ACP3\Modules\ACP3\Polls\Model\PollRepository
     */
    protected $pollRepository;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation
     */
    protected $pollsValidator;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext              $context
     * @param \ACP3\Core\Date                                         $date
     * @param \ACP3\Core\Helpers\FormToken                            $formTokenHelper
     * @param \ACP3\Modules\ACP3\Polls\Model\PollRepository           $pollRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\AnswerRepository         $answerRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\VoteRepository           $voteRepository
     * @param \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation $pollsValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Polls\Model\PollRepository $pollRepository,
        Polls\Model\AnswerRepository $answerRepository,
        Polls\Model\VoteRepository $voteRepository,
        Polls\Validation\AdminFormValidation $pollsValidator
    )
    {
        parent::__construct($context, $answerRepository, $voteRepository);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->pollRepository = $pollRepository;
        $this->pollsValidator = $pollsValidator;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        $poll = $this->pollRepository->getOneById($id);

        if (empty($poll) === false) {
            $this->breadcrumb->setTitlePostfix($poll['title']);

            if ($this->request->getPost()->has('submit')) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            if ($this->request->getPost()->has('add_answer')) {
                $answers = $this->addNewAnswer($this->request->getPost()->get('answers', []));
            } else {
                $answers = $this->answerRepository->getAnswersWithVotesByPollId($id);
            }

            $options = [
                $this->fetchMultipleChoiceOption($poll['multiple']),
                [
                    'name' => 'reset',
                    'checked' => $this->get('core.helpers.forms')->selectEntry('reset', '1', '0', 'checked'),
                    'lang' => $this->translator->t('polls', 'reset_votes')
                ]
            ];

            $this->formTokenHelper->generateFormToken();

            return [
                'answers' => $answers,
                'options' => $options,
                'form' => array_merge($poll, $this->request->getPost()->all())
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $id)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $id) {
            $this->pollsValidator->validate($formData);

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'multiple' => isset($formData['multiple']) ? '1' : '0',
                'user_id' => $this->user->getUserId(),
            ];

            $bool = $this->pollRepository->update($updateValues, $id);

            // Reset votes
            if (!empty($formData['reset'])) {
                $this->voteRepository->delete($id, 'poll_id');
            }

            $bool2 = $this->saveAnswers($formData['answers'], $id);

            $this->formTokenHelper->unsetFormToken();

            return $bool !== false && $bool2 !== false;
        });
    }
}
