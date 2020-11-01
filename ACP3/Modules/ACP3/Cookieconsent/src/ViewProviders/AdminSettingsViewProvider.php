<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Cookieconsent\ViewProviders;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Cookieconsent\Installer\Schema;

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
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        Forms $formsHelper,
        FormToken $formTokenHelper,
        RequestInterface $request,
        SettingsInterface $settings,
        Translator $translator
    ) {
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->settings = $settings;
        $this->translator = $translator;
    }

    public function __invoke(): array
    {
        $cookieConsentSettings = $this->settings->getSettings(Schema::MODULE_NAME);

        return [
            'enable' => $this->formsHelper->yesNoCheckboxGenerator(
                'enabled',
                $cookieConsentSettings['enabled']
            ),
            'type' => $this->formsHelper->choicesGenerator(
                'type',
                [
                    'opt-in' => $this->translator->t('cookieconsent', 'type_opt_in'),
                    'opt-out' => $this->translator->t('cookieconsent', 'type_opt_out'),
                    'informational' => $this->translator->t('cookieconsent', 'type_informational'),
                ],
                $cookieConsentSettings['type']
            ),
            'form' => \array_merge($cookieConsentSettings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
