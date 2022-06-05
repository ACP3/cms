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
     * @param array<string, mixed> $dbResultRow
     */
    public function __construct(private readonly OptionRenderer $optionRenderer, private readonly array $dbResultRow, private readonly string $identifier)
    {
    }

    public function getOptionRenderer(): OptionRenderer
    {
        return $this->optionRenderer;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDbResultRow(): array
    {
        return $this->dbResultRow;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
