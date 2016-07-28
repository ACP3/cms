<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\I18n\Translator;

/**
 * Class TranslateColumnRenderer
 * @package ACP3\Core\Helpers\DataGrid\ColumnRenderer
 */
class TranslateColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var
     */
    protected $translator;

    /**
     * TranslateColumnRenderer constructor.
     *
     * @param \ACP3\Core\I18n\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        if (isset($dbResultRow[$field])) {
            $value = $dbResultRow[$field];

            return $this->translator->t($value, $value);
        }

        return null;
    }
}
