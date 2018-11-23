<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Core\View;

class LayoutExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\View
     */
    private $view;

    /**
     * LayoutExistsValidationRule constructor.
     *
     * @param \ACP3\Core\View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $data === '' || $this->view->templateExists($data);
    }
}
