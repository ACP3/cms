<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\Formatter;

use ACP3\Core\I18n\Translator;

class MarkEntries
{
    public function __construct(private Translator $translator)
    {
    }

    /**
     * @throws \JsonException
     */
    public function execute(string $name, string $markAllId = ''): string
    {
        $markAllId = !empty($markAllId) ? $markAllId : 'mark-all';
        $deleteOptions = json_encode([
            'checkBoxName' => $name,
            'language' => [
                'confirmationTextSingle' => $this->translator->t('system', 'confirm_delete_single'),
                'confirmationTextMultiple' => $this->translator->t('system', 'confirm_delete_multiple'),
            ],
        ], JSON_THROW_ON_ERROR);

        return 'data-mark-all-id="' . $markAllId . '" data-checkbox-name="' . $name . '" data-delete-options=\'' . $deleteOptions . '\'';
    }
}
