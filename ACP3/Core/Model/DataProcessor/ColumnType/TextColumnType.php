<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;


use ACP3\Core\Helpers\Secure;

class TextColumnType implements ColumnTypeStrategyInterface
{
    /**
     * @var Secure
     */
    protected $secure;

    /**
     * TextColumnType constructor.
     * @param Secure $secure
     */
    public function __construct(Secure $secure)
    {
        $this->secure = $secure;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function doEscape($value)
    {
        return $this->secure->strEncode($value);
    }
}
