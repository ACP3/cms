<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\ViewProviders;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallery\Installer\Schema as GallerySchema;

class AdminSettingsViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    private $dateHelper;

    public function __construct(
        Date $dateHelper,
        Forms $formsHelper,
        FormToken $formTokenHelper,
        RequestInterface $request,
        SettingsInterface $settings
    ) {
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->settings = $settings;
        $this->dateHelper = $dateHelper;
    }

    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(GallerySchema::MODULE_NAME);

        return [
            'overlay' => $this->formsHelper->yesNoCheckboxGenerator('overlay', $settings['overlay']),
            'dateformat' => $this->dateHelper->dateFormatDropdown($settings['dateformat']),
            'sidebar_entries' => $this->formsHelper->recordsPerPage((int) $settings['sidebar'], 1, 10, 'sidebar'),
            'form' => array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
