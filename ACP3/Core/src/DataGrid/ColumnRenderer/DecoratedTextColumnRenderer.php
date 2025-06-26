<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

use ACP3\Core\Helpers\ContentDecorator;

class DecoratedTextColumnRenderer extends AbstractColumnRenderer
{
    public function __construct(private readonly ContentDecorator $contentDecorator)
    {
    }

    protected function getDbValueIfExists(array $dbResultRow, string $field): ?string
    {
        return !empty($dbResultRow[$field]) ? $this->contentDecorator->decorate($dbResultRow[$field]) : null;
    }
}
