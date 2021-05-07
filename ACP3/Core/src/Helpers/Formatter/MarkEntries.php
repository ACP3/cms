<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\Formatter;

use ACP3\Core\I18n\Translator;

class MarkEntries
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;

    /**
     * MarkEntries constructor.
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $name
     * @param string $markAllId
     *
     * @return string
     */
    public function execute($name, $markAllId = '')
    {
        $markAllId = !empty($markAllId) ? $markAllId : 'mark-all';
        $deleteOptions = json_encode(
            [
                'checkBoxName' => $name,
                'language' => [
                    'confirmationTextSingle' => $this->translator->t('system', 'confirm_delete_single'),
                    'confirmationTextMultiple' => $this->translator->t('system', 'confirm_delete_multiple'),
                    'noEntriesSelectedText' => $this->translator->t('system', 'no_entries_selected'),
                ],
                'bootboxLocale' => $this->translator->getShortIsoCode(),
            ]
        );

        return 'data-mark-all-id="' . $markAllId . '" data-checkbox-name="' . $name . '" data-delete-options=\'' . $deleteOptions . '\'';
    }
}
