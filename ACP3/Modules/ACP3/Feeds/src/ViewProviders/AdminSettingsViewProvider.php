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
    public function __construct(private Forms $formsHelper, private FormToken $formTokenHelper, private RequestInterface $request, private SettingsInterface $settings)
    {
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
