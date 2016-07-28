<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractAdminAction;
use ACP3\Modules\ACP3\Polls;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Polls\Controller\Admin\Index
 */
abstract class AbstractFormAction extends AbstractAdminAction
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
     * @param \ACP3\Core\Controller\Context\AdminContext      $context
     * @param \ACP3\Core\Helpers\Forms                        $formsHelper
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository $answerRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
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
        $options = [];
        $options[] = $this->fetchMultipleChoiceOption($useMultipleChoice);

        return $options;
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
            'checked' => $this->formsHelper->selectEntry('multiple', '1', $currentValue, 'checked'),
            'lang' => $this->translator->t('polls', 'multiple_choice')
        ];
    }
}
