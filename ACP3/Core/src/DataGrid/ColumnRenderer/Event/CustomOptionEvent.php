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
    public function __construct(private OptionRenderer $optionRenderer, private array $dbResultRow, private string $identifier)
    {
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
