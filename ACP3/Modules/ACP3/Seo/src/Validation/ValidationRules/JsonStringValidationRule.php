<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class JsonStringValidationRule extends AbstractValidationRule
{
    /**
     * {@inheritDoc}
     */
    public function isValid(float|int|bool|array|string|UploadedFile|null $data, array|string $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        if (\is_string($data) === false) {
            return false;
        }

        try {
            json_encode($data, JSON_THROW_ON_ERROR);

            return true;
        } catch (\JsonException $e) {
            return false;
        }
    }
}
