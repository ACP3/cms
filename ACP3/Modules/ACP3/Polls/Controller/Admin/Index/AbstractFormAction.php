<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AdminAction;
use ACP3\Modules\ACP3\Polls;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Polls\Controller\Admin\Index
 */
abstract class AbstractFormAction extends AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\AnswerRepository
     */
    protected $answerRepository;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\VoteRepository
     */
    protected $voteRepository;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext      $context
     * @param \ACP3\Modules\ACP3\Polls\Model\AnswerRepository $answerRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\VoteRepository   $voteRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Polls\Model\AnswerRepository $answerRepository,
        Polls\Model\VoteRepository $voteRepository
    ) {
        parent::__construct($context);

        $this->answerRepository = $answerRepository;
        $this->voteRepository = $voteRepository;
    }

    /**
     * @param array $currentAnswers
     *
     * @return array
     */
    protected function addNewAnswer(array $currentAnswers)
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

    /**
     * @param array $answers
     * @param int   $id
     *
     * @return bool|int
     */
    protected function saveAnswers(array $answers, $id)
    {
        $bool = false;
        foreach ($answers as $row) {
            // Neue Antwort hinzufügen
            if (empty($row['id'])) {
                // Neue Antwort nur hinzufügen, wenn die Löschen-Checkbox nicht gesetzt wurde
                if (!empty($row['text']) && !isset($row['delete'])) {
                    $bool = $this->answerRepository->insert(
                        ['text' => $this->get('core.helpers.secure')->strEncode($row['text']), 'poll_id' => $id]
                    );
                }
            } elseif (isset($row['delete'])) { // Antwort mitsamt Stimmen löschen
                $this->answerRepository->delete((int)$row['id']);
                $this->voteRepository->delete((int)$row['id'], 'answer_id');
            } elseif (!empty($row['text'])) { // Antwort aktualisieren
                $bool = $this->answerRepository->update(
                    ['text' => $this->get('core.helpers.secure')->strEncode($row['text'])],
                    (int)$row['id']
                );
            }
        }

        return $bool;
    }

    /**
     * @param string $currentValue
     *
     * @return array
     */
    protected function fetchMultipleChoiceOption($currentValue)
    {
        return [
            'name' => 'multiple',
            'checked' => $this->get('core.helpers.forms')->selectEntry('multiple', '1', $currentValue, 'checked'),
            'lang' => $this->translator->t('polls', 'multiple_choice')
        ];
    }
}
