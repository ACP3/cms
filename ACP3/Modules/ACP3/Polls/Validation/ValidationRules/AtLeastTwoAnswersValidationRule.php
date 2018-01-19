<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Validation\ValidationRules;

class AtLeastTwoAnswersValidationRule extends AbstractAnswerValidationRule
{
    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        if (\is_array($data)) {
            list(, $notEmptyAnswers) = $this->validateAnswers($data);

            return $notEmptyAnswers > 0;
        }

        return false;
    }
}
