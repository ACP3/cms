<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Polls\Controller\Admin\Index
 */
class Create extends AbstractFormAction
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
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->has('submit')) {
            return $this->executePost($this->request->getPost()->all());
        }

        if ($this->request->getPost()->has('add_answer')) {
            $answers = $this->addNewAnswer($this->request->getPost()->get('answers', []));
        } else {
            $answers = [
                ['text' => ''],
                ['text' => '']
            ];
        }

        $defaults = [
            'title' => '',
            'start' => '',
            'end' => ''
        ];

        $options = [];
        $options[] = $this->fetchMultipleChoiceOption(0);

        return [
            'answers' => $answers,
            'options' => $options,
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->pollsValidator->validate($formData);

            $insertValues = [
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'multiple' => isset($formData['multiple']) ? '1' : '0',
                'user_id' => $this->user->getUserId(),
            ];

            $pollId = $this->pollRepository->insert($insertValues);
            $bool2 = false;

            if ($pollId !== false) {
                $bool2 = $this->saveAnswers($formData['answers'], $pollId);
            }

            $this->formTokenHelper->unsetFormToken();

            return $pollId !== false && $bool2 !== false;
        });
    }
}
