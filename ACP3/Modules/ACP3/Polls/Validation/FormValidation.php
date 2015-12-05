<?php
namespace ACP3\Modules\ACP3\Polls\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls\Validation\ValidationRules\AtLeastTwoAnswersValidationRule;
use ACP3\Modules\ACP3\Polls\Validation\ValidationRules\DeleteAllAnswersValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Polls\Validation
 */
class FormValidation extends Core\Validation\AbstractFormValidation
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
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\DateValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->lang->t('system', 'select_date')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
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
