<?php
namespace ACP3\Modules\ACP3\Polls\Validator\ValidationRules;

/**
 * Class DeleteAllAnswersValidationRule
 * @package ACP3\Modules\ACP3\Polls\Validator\ValidationRules
 */
class DeleteAllAnswersValidationRule extends AbstractAnswerValidationRule
{
    const NAME = 'polls_delete_all_answers';

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        if (is_array($data)) {
            list($markedAnswers,) = $this->validateAnswers($data);

            return count($data) - $markedAnswers >= 2;
        }

        return false;
    }
}