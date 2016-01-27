<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;
use ACP3\Core\Helpers\Formatter\MarkEntries;

/**
 * Class HeaderColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class HeaderColumnRenderer extends AbstractColumnRenderer
{
    const CELL_TYPE = 'th';

    /**
     * @var \ACP3\Core\Helpers\Formatter\MarkEntries
     */
    protected $markEntriesHelper;

    /**
     * HeaderColumnRenderer constructor.
     *
     * @param \ACP3\Core\Helpers\Formatter\MarkEntries $markEntriesHelper
     */
    public function __construct(MarkEntries $markEntriesHelper)
    {
        $this->markEntriesHelper = $markEntriesHelper;
    }

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        if ($column['type'] === MassActionColumnRenderer::class) {
            $id = preg_replace('=[^\w\d-_]=', '', $column['label']) . '-mark-all';
            $value = '<input type="checkbox" id="' . $id . '" value="1" ' . $this->markEntriesHelper->execute('entries', $id) . '>';
        } else {
            $value = $column['label'];
        }

        return $this->render($column, $value);
    }
}