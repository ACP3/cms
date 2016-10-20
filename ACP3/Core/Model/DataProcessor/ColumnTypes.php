<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\DataProcessor;


interface ColumnTypes
{
    const COLUMN_TYPE_DATETIME = 'datetime';
    const COLUMN_TYPE_INT = 'integer';
    const COLUMN_TYPE_DOUBLE = 'double';
    const COLUMN_TYPE_TEXT = 'text';
    const COLUMN_TYPE_TEXT_WYSIWYG = 'text_wysiwyg';
    const COLUMN_TYPE_RAW = 'raw';
}
