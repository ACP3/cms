<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\I18n\Translator;

/**
 * @deprecated Since version 4.30.0, to be removed in 5.0.0. Use class ACP3\Core\DataGrid\ColumnRenderer\TranslateColumnRenderer instead
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
     * {@inheritdoc}
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
