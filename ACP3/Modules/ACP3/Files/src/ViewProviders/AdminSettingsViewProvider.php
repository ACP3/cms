<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\ViewProviders;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Files\Installer\Schema as FilesSchema;

class AdminSettingsViewProvider
{
    public function __construct(private Date $dateHelper, private Forms $formsHelper, private FormToken $formTokenHelper, private SettingsInterface $settings, private Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(FilesSchema::MODULE_NAME);

        $orderBy = [
            'date' => $this->translator->t('files', 'order_by_date_descending'),
            'custom' => $this->translator->t('files', 'order_by_custom'),
        ];

        return [
            'order_by' => $this->formsHelper->choicesGenerator('order_by', $orderBy, $settings['order_by']),
            'dateformat' => $this->dateHelper->dateFormatDropdown($settings['dateformat']),
            'sidebar_entries' => $this->formsHelper->recordsPerPage((int) $settings['sidebar'], 1, 10, 'sidebar'),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
