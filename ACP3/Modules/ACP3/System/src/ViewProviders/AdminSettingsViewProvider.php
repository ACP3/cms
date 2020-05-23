<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\ViewProviders;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\WYSIWYG\WysiwygEditorRegistrar;
use ACP3\Modules\ACP3\System\Installer\Schema as SystemAlias;

class AdminSettingsViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    private $dateHelper;
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
    /**
     * @var \ACP3\Core\WYSIWYG\WysiwygEditorRegistrar
     */
    private $editorRegistrar;

    public function __construct(
        Date $dateHelper,
        Forms $formsHelper,
        FormToken $formTokenHelper,
        RequestInterface $request,
        SettingsInterface $settings,
        Translator $translator,
        WysiwygEditorRegistrar $editorRegistrar
    ) {
        $this->dateHelper = $dateHelper;
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->settings = $settings;
        $this->translator = $translator;
        $this->editorRegistrar = $editorRegistrar;
    }

    public function __invoke(): array
    {
        $systemSettings = $this->settings->getSettings(SystemAlias::MODULE_NAME);

        $siteSubtitleMode = [
            1 => $this->translator->t('system', 'site_subtitle_mode_all_pages'),
            2 => $this->translator->t('system', 'site_subtitle_mode_homepage_only'),
            3 => $this->translator->t('system', 'site_subtitle_mode_never'),
        ];

        $pageCachePurgeMode = [
            1 => $this->translator->t('system', 'page_cache_purge_mode_automatically'),
            2 => $this->translator->t('system', 'page_cache_purge_mode_manually'),
        ];

        $mailerTypes = [
            'mail' => $this->translator->t('system', 'mailer_type_php_mail'),
            'smtp' => $this->translator->t('system', 'mailer_type_smtp'),
        ];

        $mailerSmtpSecurity = [
            'none' => $this->translator->t('system', 'mailer_smtp_security_none'),
            'ssl' => $this->translator->t('system', 'mailer_smtp_security_ssl'),
            'tls' => $this->translator->t('system', 'mailer_smtp_security_tls'),
        ];

        return [
            'site_subtitle_mode' => $this->formsHelper->checkboxGenerator(
                'site_subtitle_mode',
                $siteSubtitleMode,
                $systemSettings['site_subtitle_mode']
            ),
            'site_subtitle_homepage_mode' => $this->formsHelper->yesNoCheckboxGenerator(
                'site_subtitle_homepage_mode',
                $systemSettings['site_subtitle_homepage_mode']
            ),
            'cookie_consent' => $this->formsHelper->yesNoCheckboxGenerator(
                'cookie_consent_is_enabled',
                $systemSettings['cookie_consent_is_enabled']
            ),
            'entries' => $this->formsHelper->recordsPerPage($systemSettings['entries']),
            'wysiwyg' => $this->fetchWysiwygEditors($systemSettings['wysiwyg']),
            'languages' => $this->translator->getLanguagePacks($systemSettings['lang']),
            'mod_rewrite' => $this->formsHelper->yesNoCheckboxGenerator('mod_rewrite', $systemSettings['mod_rewrite']),
            'time_zones' => $this->dateHelper->getTimeZones($systemSettings['date_time_zone']),
            'maintenance' => $this->formsHelper->yesNoCheckboxGenerator(
                'maintenance_mode',
                $systemSettings['maintenance_mode']
            ),
            'page_cache' => $this->formsHelper->yesNoCheckboxGenerator(
                'page_cache_is_enabled',
                $systemSettings['page_cache_is_enabled']
            ),
            'page_cache_purge_mode' => $this->formsHelper->checkboxGenerator(
                'page_cache_purge_mode',
                $pageCachePurgeMode,
                $systemSettings['page_cache_purge_mode']
            ),
            'cache_images' => $this->formsHelper->yesNoCheckboxGenerator(
                'cache_images',
                $systemSettings['cache_images']
            ),
            'mailer_type' => $this->formsHelper->choicesGenerator(
                'mailer_type',
                $mailerTypes,
                $systemSettings['mailer_type']
            ),
            'mailer_smtp_auth' => $this->formsHelper->yesNoCheckboxGenerator(
                'mailer_smtp_auth',
                $systemSettings['mailer_smtp_auth']
            ),
            'mailer_smtp_security' => $this->formsHelper->choicesGenerator(
                'mailer_smtp_security',
                $mailerSmtpSecurity,
                $systemSettings['mailer_smtp_security']
            ),
            'form' => \array_merge($systemSettings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    private function fetchWysiwygEditors(string $currentWysiwygEditor): array
    {
        $wysiwyg = [];
        foreach ($this->editorRegistrar->all() as $serviceId => $editorInstance) {
            if ($editorInstance->isValid()) {
                $wysiwyg[$serviceId] = $editorInstance->getFriendlyName();
            }
        }

        return $this->formsHelper->choicesGenerator('wysiwyg', $wysiwyg, $currentWysiwygEditor);
    }
}
