<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer\Event;

use ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer;
use Symfony\Component\EventDispatcher\Event;

class CustomOptionEvent extends Event
{
    /**
     * @var \ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer
     */
    protected $optionRenderer;
    /**
     * @var array
     */
    private $dbResultRow;
    /**
     * @var string
     */
    protected $identifier;

    /**
     * CustomOptionEvent constructor.
     *
     * @param \ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer $optionRenderer
     * @param array                                                                          $dbResultRow
     * @param string                                                                         $identifier
     */
    public function __construct(OptionRenderer $optionRenderer, array $dbResultRow, $identifier)
    {
        $this->optionRenderer = $optionRenderer;
        $this->identifier = $identifier;
        $this->dbResultRow = $dbResultRow;
    }

    /**
     * @return \ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer
     */
    public function getOptionRenderer()
    {
        return $this->optionRenderer;
    }

    /**
     * @return array
     */
    public function getDbResultRow()
    {
        return $this->dbResultRow;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
