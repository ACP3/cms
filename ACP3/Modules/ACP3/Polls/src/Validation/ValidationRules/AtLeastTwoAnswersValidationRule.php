<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Validation\ValidationRules;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class AtLeastTwoAnswersValidationRule extends AbstractAnswerValidationRule
{
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
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
