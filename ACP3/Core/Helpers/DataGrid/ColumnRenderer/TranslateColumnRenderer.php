<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;
use ACP3\Core\Lang;

/**
 * Class TranslateColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class TranslateColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var
     */
    protected $lang;

    /**
     * TranslateColumnRenderer constructor.
     *
     * @param \ACP3\Core\Lang $lang
     */
    public function __construct(Lang $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow, $identifier)
    {
        $value = $this->getDbFieldValueIfExists($column, $dbResultRow);

        return $this->render($column, $this->lang->t($value, $value));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'translate';
    }
}