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
    public function doEscape($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function doUnescape($value)
    {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultValue()
    {
        return '';
    }
}
