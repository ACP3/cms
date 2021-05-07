<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\ViewProviders;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Feeds\Installer\Schema as FeedsSchema;

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

    public function __construct(
        Forms $formsHelper,
        FormToken $formTokenHelper,
        RequestInterface $request,
        SettingsInterface $settings
    ) {
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->settings = $settings;
    }

    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(FeedsSchema::MODULE_NAME);

        $feedTypes = [
            'RSS 1.0' => 'RSS 1.0',
            'RSS 2.0' => 'RSS 2.0',
            'ATOM' => 'ATOM',
        ];

        return [
            'feed_types' => $this->formsHelper->choicesGenerator('feed_type', $feedTypes, $settings['feed_type']),
            'form' => array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
