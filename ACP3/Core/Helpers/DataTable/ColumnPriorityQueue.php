<?php
namespace ACP3\Core\Helpers\DataTable;

/**
 * Class ColumnPriorityQueue
 * @package ACP3\Core\Helpers\DataTable
 */
class ColumnPriorityQueue extends \SplPriorityQueue
{
    /**
     * @var int
     */
    protected $serial = PHP_INT_MAX;

    /**
     * @inheritdoc
     *
     * @see http://php.net/manual/en/splpriorityqueue.compare.php#93999
     */
    public function insert($value, $priority)
    {
        parent::insert($value, [$priority, $this->serial--]);
    }
}