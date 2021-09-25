<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer\Event;

use ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer;
use Symfony\Contracts\EventDispatcher\Event;

class CustomOptionEvent extends Event
{
    /**
     * @var \ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer
     */
    private $optionRenderer;
    /**
     * @var array
     */
    private $dbResultRow;
    /**
     * @var string
     */
    private $identifier;

    public function __construct(OptionRenderer $optionRenderer, array $dbResultRow, string $identifier)
    {
        $this->optionRenderer = $optionRenderer;
        $this->identifier = $identifier;
        $this->dbResultRow = $dbResultRow;
    }

    public function getOptionRenderer(): OptionRenderer
    {
        return $this->optionRenderer;
    }

    public function getDbResultRow(): array
    {
        return $this->dbResultRow;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
