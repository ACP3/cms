<?php
namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\I18n\TranslatorInterface;

class TranslateColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * TranslateColumnRenderer constructor.
     *
     * @param \ACP3\Core\I18n\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
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
