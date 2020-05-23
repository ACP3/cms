<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\ViewProviders;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;

class AdminCommentsSettingsViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    private $dateHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(Date $dateHelper, FormToken $formTokenHelper, SettingsInterface $settings)
    {
        $this->dateHelper = $dateHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->settings = $settings;
    }

    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(CommentsSchema::MODULE_NAME);

        return [
            'dateformat' => $this->dateHelper->dateFormatDropdown($settings['dateformat']),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
