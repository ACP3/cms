<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class TextWysiwygColumnType implements ColumnTypeStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function doEscape($value): ?string
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function doUnescape($value): ?string
    {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultValue(): string
    {
        return '';
    }
}
