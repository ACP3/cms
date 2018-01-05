<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class TextWysiwygColumnType extends TextColumnType
{
    /**
     * @inheritdoc
     */
    public function doEscape($value)
    {
        return $this->secure->strEncode($value, true);
    }
}
