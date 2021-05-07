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
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $forms;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        Forms $forms,
        RequestInterface $request,
        SettingsInterface $settings,
        Translator $translator
    ) {
        $this->request = $request;
        $this->settings = $settings;
        $this->forms = $forms;
        $this->translator = $translator;
    }

    public function __invoke(): array
    {
        $prefix = $this->request->getArea() === AreaEnum::AREA_ADMIN ? 'acp/' : '';
        $currentPage = base64_encode($prefix . $this->request->getQuery());
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
