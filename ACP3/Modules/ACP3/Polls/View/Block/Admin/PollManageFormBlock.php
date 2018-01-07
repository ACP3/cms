<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\View\Block\Admin;

use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollAnswersRepository;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollsRepository;

class PollManageFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @var PollAnswersRepository
     */
    private $answerRepository;

    /**
     * PollFormBlock constructor.
     *
     * @param FormBlockContext      $context
     * @param PollsRepository       $pollsRepository
     * @param PollAnswersRepository $answerRepository
     */
    public function __construct(
        FormBlockContext $context,
        PollsRepository $pollsRepository,
        PollAnswersRepository $answerRepository
    ) {
        parent::__construct($context, $pollsRepository);

        $this->answerRepository = $answerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $poll = $this->getData();

        $this->breadcrumb->setLastStepReplacement(
            $this->translator->t('polls', !$this->getId() ? 'admin_index_create' : 'admin_index_edit')
        );

        $this->title->setPageTitlePrefix($poll['title']);

        return [
            'answers' => $this->getAnswers($poll['id']),
            'options' => $this->fetchOptions($poll['multiple']),
            'form' => \array_merge($poll, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }

    /**
     * @param int $pollId
     *
     * @return array
     */
    protected function getAnswers(int $pollId = 0): array
    {
        $formData = $this->getRequestData();

        if (isset($formData['add_answer'])) {
            return $this->addNewAnswer($formData['answers'] ?? []);
        } elseif (!empty($pollId)) {
            return $this->answerRepository->getAnswersWithVotesByPollId($pollId);
        }

        return [
            ['text' => ''],
            ['text' => ''],
        ];
    }

    /**
     * @param array $currentAnswers
     *
     * @return array
     */
    private function addNewAnswer(array $currentAnswers): array
    {
        $answers = [];

        // Current answers
        $i = 0;
        foreach ($currentAnswers as $row) {
            if (isset($row['id'])) {
                $answers[$i]['id'] = $row['id'];
            }
            $answers[$i]['text'] = $row['text'];
            ++$i;
        }

        // Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
        if (empty($currentAnswers) || !empty($currentAnswers[$i - 1]['text'])) {
            $answers[$i]['text'] = '';
        }

        return $answers;
    }

    /**
     * @param int $useMultipleChoice
     *
     * @return array
     */
    private function fetchOptions(int $useMultipleChoice): array
    {
        $data = $this->getData();

        $reset = [
            '1' => $this->translator->t('polls', 'reset_votes'),
        ];

        return \array_merge(
            $this->fetchMultipleChoiceOption($useMultipleChoice),
            !empty($data['id'])
                ? $this->forms->checkboxGenerator('reset', $reset, '0')
                : []
        );
    }

    /**
     * @param string $currentValue
     *
     * @return array
     */
    private function fetchMultipleChoiceOption(string $currentValue): array
    {
        $values = [
            '1' => $this->translator->t('polls', 'multiple_choice'),
        ];

        return $this->forms->checkboxGenerator('multiple', $values, $currentValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [
            'id' => 0,
            'start' => '',
            'end' => '',
            'title' => '',
            'multiple' => 0,
        ];
    }
}
