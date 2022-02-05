<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use JsonSchema\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StructuredDataValidationRule extends AbstractValidationRule
{
    public function __construct(private Validator $jsonSchemaValidator)
    {
    }

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
            $json = json_decode($data, false, 512, JSON_THROW_ON_ERROR);
            $this->jsonSchemaValidator->validate(
                $json,
                (object) ['$ref' => 'file://' . realpath(__DIR__ . '/../../../Resources/json-schema/json-ld-schema.json')]
            );

            return $this->jsonSchemaValidator->isValid();
        } catch (\JsonException $e) {
            return false;
        }
    }
}
