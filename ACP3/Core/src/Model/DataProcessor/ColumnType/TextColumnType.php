<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
     *
     * @param Secure $secure
     */
    public function __construct(Secure $secure)
    {
        $this->secure = $secure;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function doEscape($value)
    {
        return $this->secure->strEncode($value);
    }

    /**
     * {@inheritdoc}
     */
    public function doUnescape($value)
    {
        return \html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return string|int
     */
    public function getDefaultValue()
    {
        return '';
    }
}
