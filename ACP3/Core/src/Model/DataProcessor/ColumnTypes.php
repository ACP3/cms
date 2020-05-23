<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor;

interface ColumnTypes
{
    public const COLUMN_TYPE_DATE = 'date';
    public const COLUMN_TYPE_DATETIME = 'datetime';
    public const COLUMN_TYPE_BOOLEAN = 'boolean';
    public const COLUMN_TYPE_INT = 'integer';
    public const COLUMN_TYPE_INT_NULLABLE = 'integer_nullable';
    public const COLUMN_TYPE_DOUBLE = 'double';
    public const COLUMN_TYPE_TEXT = 'text';
    public const COLUMN_TYPE_TEXT_WYSIWYG = 'text_wysiwyg';
    public const COLUMN_TYPE_RAW = 'raw';
    public const COLUMN_TYPE_SERIALIZABLE = 'serializable';
}
