<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\ViewProviders;

use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Categories\Installer\Schema;

class AdminCategorySettingsViewProvider
{
    public function __construct(private readonly FormToken $formTokenHelper, private readonly RequestInterface $request, private readonly SettingsInterface $settings)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        return [
            'form' => array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
