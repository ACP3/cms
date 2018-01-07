<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\DataGrid;

class ColumnPriorityQueue extends \SplPriorityQueue
{
    /**
     * @var int
     */
    protected $serial = PHP_INT_MAX;

    /**
     * {@inheritdoc}
     *
     * @see http://php.net/manual/en/splpriorityqueue.compare.php#93999
     */
    public function insert($value, $priority)
    {
        parent::insert($value, [$priority, $this->serial--]);
    }
}
