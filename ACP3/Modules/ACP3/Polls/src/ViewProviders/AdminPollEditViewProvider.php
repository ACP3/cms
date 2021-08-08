<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\ViewProviders;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Polls\Repository\AnswerRepository;

class AdminPollEditViewProvider
{
    /**
     * @var \ACP3\Modules\ACP3\Polls\Repository\AnswerRepository
     */
    private $answerRepository;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        AnswerRepository $answerRepository,
        Forms $formsHelper,
        FormToken $formTokenHelper,
        RequestInterface $request,
        Title $title,
        Translator $translator
    ) {
        $this->answerRepository = $answerRepository;
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->title = $title;
        $this->translator = $translator;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(array $poll): array
    {
        $this->title->setPageTitlePrefix($poll['title']);

        return [
            'answers' => $this->getAnswers($poll['id'] ?? null),
            'options' => $this->fetchOptions(!empty($poll['id']), $poll['multiple']),
            'form' => array_merge($poll, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function getAnswers(?int $pollId): array
    {
        if ($this->request->getPost()->has('add_answer')) {
            return $this->addNewAnswer($this->request->getPost()->get('answers', []));
        }

        if ($pollId === null) {
            return [
                ['text' => ''],
                ['text' => ''],
            ];
        }

        return $this->answerRepository->getAnswersWithVotesByPollId($pollId);
    }

    private function addNewAnswer(array $currentAnswers): array
    {
        $answers = [];

        // Bisherige Antworten
        $i = 0;
        foreach ($currentAnswers as $row) {
            if (isset($row['id'])) {
                $answers[$i]['id'] = $row['id'];
            }
            $answers[$i]['text'] = $row['text'];
            ++$i;
        }

        // Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
        if (!empty($currentAnswers[$i - 1]['text'])) {
            $answers[$i]['text'] = '';
        }

        return $answers;
    }

    private function fetchOptions(bool $isExistingPoll, int $useMultipleChoice): array
    {
        $values = [
            1 => $this->translator->t('polls', 'multiple_choice'),
        ];

        $options = $this->formsHelper->checkboxGenerator('multiple', $values, $useMultipleChoice);

        if ($isExistingPoll === true) {
            $reset = [
                1 => $this->translator->t('polls', 'reset_votes'),
            ];

            $options = array_merge(
                $options,
                $this->formsHelper->checkboxGenerator('reset', $reset, 0)
            );
        }

        return $options;
    }
}
