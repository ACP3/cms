<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Polls;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Polls\Controller\Admin\Index
 */
abstract class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository
     */
    protected $answerRepository;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext      $context
     * @param \ACP3\Core\Helpers\Forms                        $formsHelper
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository $answerRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Polls\Model\Repository\AnswerRepository $answerRepository
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->answerRepository = $answerRepository;
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
     * @param int $useMultipleChoice
     * @return array
     */
    protected function fetchOptions($useMultipleChoice)
    {
        return $this->fetchMultipleChoiceOption($useMultipleChoice);
    }

    /**
     * @param string $currentValue
     *
     * @return array
     */
    protected function fetchMultipleChoiceOption($currentValue)
    {
        $values = [
            '1' => $this->translator->t('polls', 'multiple_choice')
        ];
        return $this->formsHelper->checkboxGenerator('multiple', $values, $currentValue);
    }
}
