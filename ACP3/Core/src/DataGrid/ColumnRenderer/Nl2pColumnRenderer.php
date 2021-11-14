<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

use ACP3\Core\Helpers\StringFormatter;

class Nl2pColumnRenderer extends AbstractColumnRenderer
{
    public function __construct(private StringFormatter $stringFormatter)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function getDbValueIfExists(array $dbResultRow, $field): ?string
    {
        return !empty($dbResultRow[$field]) ? $this->stringFormatter->nl2p($dbResultRow[$field]) : null;
    }
}
