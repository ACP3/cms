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
     * {@inheritdoc}
     */
    protected function getValue(array $column, array $dbResultRow)
    {
        $value = parent::getValue($column, $dbResultRow);

        if (!empty($value)) {
            $domain = $column['custom']['domain'] ?? $value;

            $value = $this->translator->t($domain, $value);
        }

        return $value;
    }
}
