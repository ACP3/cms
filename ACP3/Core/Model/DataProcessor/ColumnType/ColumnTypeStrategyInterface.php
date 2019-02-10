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
     * This method is the counterpart of the ::doEscape() method.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function doUnescape($value);

    /**
     * @return mixed
     */
    public function getDefaultValue();
}
