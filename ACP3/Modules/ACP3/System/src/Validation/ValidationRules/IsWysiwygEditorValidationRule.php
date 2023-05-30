<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class IsWysiwygEditorValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly ContainerInterface $editorLocator)
    {
    }

    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->isValidWysiwygEditor($data);
    }

    /**
     * @param string $data
     *
     * @return bool
     */
    protected function isValidWysiwygEditor($data)
    {
        return !empty($data) && $this->editorLocator->has($data) && $this->editorLocator->get($data)->isValid();
    }
}
