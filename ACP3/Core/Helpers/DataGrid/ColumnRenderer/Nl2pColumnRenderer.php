<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;
use ACP3\Core\Helpers\StringFormatter;

/**
 * Class Nl2pColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class Nl2pColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    protected $stringFormatter;

    /**
     * Nl2pColumnRenderer constructor.
     *
     * @param \ACP3\Core\Helpers\StringFormatter $stringFormatter
     */
    public function __construct(StringFormatter $stringFormatter)
    {
        $this->stringFormatter = $stringFormatter;
    }

    /**
     * @inheritdoc
     */
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        return isset($dbResultRow[$field]) ? $this->stringFormatter->nl2p($dbResultRow[$field]) : null;
    }
}