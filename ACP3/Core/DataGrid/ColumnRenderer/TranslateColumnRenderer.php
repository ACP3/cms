<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

use ACP3\Core\I18n\Translator;

class TranslateColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\I18n\Translator
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
     * {@inheritdoc}
     */
    protected function getValue(array $column, array $dbResultRow)
    {
        $value = parent::getValue($column, $dbResultRow);
        $fields = $this->getDbFields($column);

        if (\count($fields) === 2) {
            $value = $this->translator->t($dbResultRow[\reset($fields)], $dbResultRow[\next($fields)]);
        } elseif (!empty($value)) {
            $domain = $column['custom']['domain'] ?? $value;

            $value = $this->translator->t($domain, $value);
        }

        if (empty($value) && !empty($this->getDefaultValue($column))) {
            $value = $this->getDefaultValue($column);
        }

        return $value;
    }
}
