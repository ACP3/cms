<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

interface ColumnTypeStrategyInterface
{
    public function doEscape(mixed $value);

    /**
     * This method is the counterpart of the ::doEscape() method.
     */
    public function doUnescape(mixed $value): mixed;

    /**
     * @return string|int|null
     */
    public function getDefaultValue();
}
