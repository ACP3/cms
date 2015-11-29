<?php
namespace ACP3\Modules\ACP3\Polls\Validator\ValidationRules;

use ACP3\Core\Validator\ValidationRules\AbstractValidationRule;
use ACP3\Core\Validator\ValidationRules\NotEmptyValidationRule;

/**
 * Class AbstractAnswerValidationRule
 * @package ACP3\Modules\ACP3\Polls\Validator\ValidationRules
 */
abstract class AbstractAnswerValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\Validator\ValidationRules\NotEmptyValidationRule
     */
    protected $notEmptyValidationRule;

    /**
     * AbstractAnswerValidationRule constructor.
     *
     * @param \ACP3\Core\Validator\ValidationRules\NotEmptyValidationRule $notEmptyValidationRule
     */
    public function __construct(NotEmptyValidationRule $notEmptyValidationRule)
    {
        $this->notEmptyValidationRule = $notEmptyValidationRule;
    }

    /**
     * @param array $answers
     *
     * @return array
     */
    protected function validateAnswers(array $answers)
    {
        $markedAnswers = 0;
        $notEmptyAnswers = 0;
        foreach ($answers as $row) {
            if ($this->notEmptyValidationRule->isValid($row['text'])) {
                ++$notEmptyAnswers;
            }
            if (isset($row['delete'])) {
                ++$markedAnswers;
            }
        }
        return [$markedAnswers, $notEmptyAnswers];
    }
}