<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\ViewProviders;

use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Contact\Installer\Schema as ContactSchema;
use ACP3\Modules\ACP3\System\ViewProviders\BackUriTrait;

class AdminSettingsViewProvider
{
    use BackUriTrait;

    public function __construct(
        private readonly FormToken $formTokenHelper,
        private readonly RequestInterface $request,
        private readonly RouterInterface $router,
        private readonly SettingsInterface $settings,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(ContactSchema::MODULE_NAME);

        return [
            'form' => array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
            'BACK_URI' => $this->getBackUri($this->router->route('acp/contact', true)),
        ];
    }
}
