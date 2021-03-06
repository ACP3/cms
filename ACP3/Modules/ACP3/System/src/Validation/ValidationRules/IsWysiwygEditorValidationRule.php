<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use Psr\Container\ContainerInterface;

class IsWysiwygEditorValidationRule extends AbstractValidationRule
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $editorLocator;

    public function __construct(ContainerInterface $editorLocator)
    {
        $this->editorLocator = $editorLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
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
