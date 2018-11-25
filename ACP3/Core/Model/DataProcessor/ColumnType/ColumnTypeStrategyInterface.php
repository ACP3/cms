<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

interface ColumnTypeStrategyInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function doEscape($value);

    /**
     * @return string|int
     */
    public function getDefaultValue();
}
