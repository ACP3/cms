<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls\Validation\ValidationRules\AtLeastTwoAnswersValidationRule;
use ACP3\Modules\ACP3\Polls\Validation\ValidationRules\DeleteAllAnswersValidationRule;

class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\DateValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->translator->t('system', 'select_date'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('polls', 'type_in_question'),
                ]
            )
            ->addConstraint(
                AtLeastTwoAnswersValidationRule::class,
                [
                    'data' => $formData['answers'],
                    'field' => 'answer',
                    'message' => $this->translator->t('polls', 'type_in_two_answers'),
                ]
            )
            ->addConstraint(
                DeleteAllAnswersValidationRule::class,
                [
                    'data' => $formData['answers'],
                    'field' => 'answer',
                    'message' => $this->translator->t('polls', 'can_not_delete_all_answers'),
                ]
            );

        $this->validator->validate();
    }
}
