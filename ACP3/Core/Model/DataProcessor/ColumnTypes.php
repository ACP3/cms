<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\DataProcessor;

interface ColumnTypes
{
    const COLUMN_TYPE_DATETIME = 'datetime';
    const COLUMN_TYPE_BOOLEAN = 'boolean';
    const COLUMN_TYPE_INT = 'integer';
    const COLUMN_TYPE_INT_NULLABLE = 'integer_nullable';
    const COLUMN_TYPE_DOUBLE = 'double';
    const COLUMN_TYPE_TEXT = 'text';
    const COLUMN_TYPE_TEXT_WYSIWYG = 'text_wysiwyg';
    const COLUMN_TYPE_RAW = 'raw';
}
