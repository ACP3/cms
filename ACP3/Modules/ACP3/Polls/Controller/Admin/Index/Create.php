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
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository
     */
    protected $pollRepository;
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
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository $pollRepository
     * @param Polls\Model\PollsModel $pollsModel
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository $answerRepository
     * @param \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation $pollsValidator
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Polls\Model\Repository\PollRepository $pollRepository,
        Polls\Model\PollsModel $pollsModel,
        Polls\Model\Repository\AnswerRepository $answerRepository,
        Polls\Validation\AdminFormValidation $pollsValidator
    ) {
        parent::__construct($context, $formsHelper, $answerRepository);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->pollRepository = $pollRepository;
        $this->pollsModel = $pollsModel;
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

        $defaults = [
            'title' => '',
            'start' => '',
            'end' => ''
        ];

        return [
            'answers' => $this->getAnswers(),
            'options' => $this->fetchOptions(0),
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

            $pollId = $this->pollsModel->savePoll($formData, $this->user->getUserId());

            $bool2 = false;
            if ($pollId !== false) {
                $bool2 = $this->pollsModel->saveAnswers($formData['answers'], $pollId);
            }

            return $pollId !== false && $bool2 !== false;
        });
    }

    /**
     * @return array
     */
    protected function getAnswers()
    {
        if ($this->request->getPost()->has('add_answer')) {
            $answers = $this->addNewAnswer($this->request->getPost()->get('answers', []));
        } else {
            $answers = [
                ['text' => ''],
                ['text' => '']
            ];
        }

        return $answers;
    }
}
