<?php
namespace ACP3\Core\Helpers\DataTable;

use ACP3\Core\Helpers\Formatter\MarkEntries;

/**
 * Class HeaderColumnRenderer
 * @package ACP3\Core\Helpers\DataTable
 */
class HeaderColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\Helpers\Formatter\MarkEntries
     */
    protected $markEntries;

    /**
     * @param \ACP3\Core\Helpers\Formatter\MarkEntries $markEntries
     */
    public function __construct(MarkEntries $markEntries)
    {
        $this->markEntries = $markEntries;
    }

    /**
     * @param \ACP3\Core\Helpers\DataTable\ColumnPriorityQueue $columns
     *
     * @return string
     */
    public function renderTableHeader(ColumnPriorityQueue $columns)
    {
        $header = '';

        foreach (clone $columns as $column) {
            if ($column['type'] === 'mass_delete') {
                $id = $column['label'] . '-mark-all';
                $value = '<input type="checkbox" id="' . $id . '" value="1" ' . $this->markEntries->execute('entries', $id) . '>';
            } else {
                $value = $column['label'];
            }

            $header .= $this->renderColumn($value, self::TYPE_TH, [], $column['class'], $column['style']);
        }

        return $header;
    }
}