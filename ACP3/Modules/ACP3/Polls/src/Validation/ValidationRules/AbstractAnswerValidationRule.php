<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Core\Validation\ValidationRules\NotEmptyValidationRule;

abstract class AbstractAnswerValidationRule extends AbstractValidationRule
{
    public function __construct(private NotEmptyValidationRule $notEmptyValidationRule)
    {
    }

    protected function validateAnswers(array $answers): array
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
