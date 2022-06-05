<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Users\Installer\Schema;

class LoginViewProvider
{
    public function __construct(private readonly Forms $forms, private readonly RequestInterface $request, private readonly SettingsInterface $settings, private readonly Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(?string $redirect = null): array
    {
        $prefix = $this->request->getArea() === AreaEnum::AREA_ADMIN ? 'acp/' : '';
        $currentPage = $redirect ?? base64_encode($prefix . $this->request->getQuery());
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $rememberMe = [
            1 => $this->translator->t('users', 'remember_me'),
        ];

        return [
            'remember_me' => $this->forms->checkboxGenerator('remember', $rememberMe, 0),
            'enable_registration' => $settings['enable_registration'],
            'redirect_uri' => $this->request->getPost()->get('redirect_uri', $currentPage),
        ];
    }
}
