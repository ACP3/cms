<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\I18n\Translator;

/**
 * Class TranslateColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class TranslateColumnRenderer extends AbstractColumnRenderer
{
    const NAME = 'translate';

    /**
     * @var
     */
    protected $lang;

    /**
     * TranslateColumnRenderer constructor.
     *
     * @param \ACP3\Core\I18n\Translator $lang
     */
    public function __construct(Translator $lang)
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
}