<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

use ACP3\Core\Helpers\Secure;

class TextColumnType implements ColumnTypeStrategyInterface
{
    public function __construct(protected Secure $secure)
    {
    }

    /**
     * @param mixed $value
     */
    public function doEscape($value): string
    {
        return $this->secure->strEncode($value);
    }

    /**
     * {@inheritdoc}
     */
    public function doUnescape($value): string
    {
        return html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public function getDefaultValue(): string
    {
        return '';
    }
}
