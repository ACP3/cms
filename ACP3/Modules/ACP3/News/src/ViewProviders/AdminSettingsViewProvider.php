<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\ViewProviders;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\News\Installer\Schema as NewsSchema;

class AdminSettingsViewProvider
{
    public function __construct(private readonly Date $dateHelper, private readonly Forms $formsHelper, private readonly FormToken $formTokenHelper, private readonly RequestInterface $request, private readonly SettingsInterface $settings)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(NewsSchema::MODULE_NAME);

        return [
            'dateformat' => $this->dateHelper->dateFormatDropdown($settings['dateformat']),
            'readmore' => $this->formsHelper->yesNoCheckboxGenerator('readmore', $settings['readmore']),
            'readmore_chars' => $this->request->getPost()->get('readmore_chars', $settings['readmore_chars']),
            'sidebar_entries' => $this->formsHelper->recordsPerPage((int) $settings['sidebar'], 1, 10, 'sidebar'),
            'category_in_breadcrumb' => $this->formsHelper->yesNoCheckboxGenerator(
                'category_in_breadcrumb',
                $settings['category_in_breadcrumb']
            ),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
