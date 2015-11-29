<?php
namespace ACP3\Modules\ACP3\Polls;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls\Validator\ValidationRules\AtLeastTwoAnswersValidationRule;
use ACP3\Modules\ACP3\Polls\Validator\ValidationRules\DeleteAllAnswersValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Polls
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\DateValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->lang->t('system', 'select_date')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('polls', 'type_in_question')
                ])
            ->addConstraint(
                AtLeastTwoAnswersValidationRule::NAME,
                [
                    'data' => $formData['answers'],
                    'field' => 'answer',
                    'message' => $this->lang->t('polls', 'type_in_two_answers')
                ])
            ->addConstraint(
                DeleteAllAnswersValidationRule::NAME,
                [
                    'data' => $formData['answers'],
                    'field' => 'answer',
                    'message' => $this->lang->t('polls', 'can_not_delete_all_answers')
                ]);

        $this->validator->validate();
    }
}
