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
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        if (isset($dbResultRow[$field])) {
            $value = $dbResultRow[$field];

            return $this->lang->t($value, $value);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'translate';
    }
}