<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\View\Block\Admin;


use ACP3\Core\Helpers\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractSettingsFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Core\WYSIWYG\WysiwygEditorRegistrar;
use ACP3\Modules\ACP3\System\Installer\Schema;

class SystemSettingsFormBlock extends AbstractSettingsFormBlock
{
    /**
     * @var Date
     */
    private $dateHelper;
    /**
     * @var WysiwygEditorRegistrar
     */
    private $editorRegistrar;

    /**
     * SystemSettingsFormBlock constructor.
     * @param FormBlockContext $context
     * @param SettingsInterface $settings
     * @param Date $dateHelper
     * @param WysiwygEditorRegistrar $editorRegistrar
     */
    public function __construct(
        FormBlockContext $context,
        SettingsInterface $settings,
        Date $dateHelper,
        WysiwygEditorRegistrar $editorRegistrar
    ) {
        parent::__construct($context, $settings);

        $this->dateHelper = $dateHelper;
        $this->editorRegistrar = $editorRegistrar;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $systemSettings = $this->getData();

        $siteSubtitleMode = [
            1 => $this->translator->t('system', 'site_subtitle_mode_all_pages'),
            2 => $this->translator->t('system', 'site_subtitle_mode_homepage_only'),
        ];

        $pageCachePurgeMode = [
            1 => $this->translator->t('system', 'page_cache_purge_mode_automatically'),
            2 => $this->translator->t('system', 'page_cache_purge_mode_manually'),
        ];

        $mailerTypes = [
            'mail' => $this->translator->t('system', 'mailer_type_php_mail'),
            'smtp' => $this->translator->t('system', 'mailer_type_smtp')
        ];

        $mailerSmtpSecurity = [
            'none' => $this->translator->t('system', 'mailer_smtp_security_none'),
            'ssl' => $this->translator->t('system', 'mailer_smtp_security_ssl'),
            'tls' => $this->translator->t('system', 'mailer_smtp_security_tls')
        ];

        return [
            'site_subtitle_mode' => $this->forms->checkboxGenerator(
                'site_subtitle_mode',
                $siteSubtitleMode,
                $systemSettings['site_subtitle_mode']
            ),
            'site_subtitle_homepage_mode' => $this->forms->yesNoCheckboxGenerator(
                'site_subtitle_homepage_mode',
                $systemSettings['site_subtitle_homepage_mode']
            ),
            'cookie_consent' => $this->forms->yesNoCheckboxGenerator(
                'cookie_consent_is_enabled',
                $systemSettings['cookie_consent_is_enabled']
            ),
            'entries' => $this->forms->recordsPerPage($systemSettings['entries']),
            'wysiwyg' => $this->fetchWysiwygEditors($systemSettings['wysiwyg']),
            'languages' => $this->translator->getLanguagePack($systemSettings['lang']),
            'mod_rewrite' => $this->forms->yesNoCheckboxGenerator('mod_rewrite', $systemSettings['mod_rewrite']),
            'time_zones' => $this->dateHelper->getTimeZones($systemSettings['date_time_zone']),
            'maintenance' => $this->forms->yesNoCheckboxGenerator(
                'maintenance_mode',
                $systemSettings['maintenance_mode']
            ),
            'page_cache' => $this->forms->yesNoCheckboxGenerator(
                'page_cache_is_enabled',
                $systemSettings['page_cache_is_enabled']
            ),
            'page_cache_purge_mode' => $this->forms->checkboxGenerator(
                'page_cache_purge_mode',
                $pageCachePurgeMode,
                $systemSettings['page_cache_purge_mode']
            ),
            'cache_images' => $this->forms->yesNoCheckboxGenerator(
                'cache_images',
                $systemSettings['cache_images']
            ),
            'mailer_type' => $this->forms->choicesGenerator(
                'mailer_type',
                $mailerTypes,
                $systemSettings['mailer_type']
            ),
            'mailer_smtp_auth' => $this->forms->yesNoCheckboxGenerator(
                'mailer_smtp_auth',
                $systemSettings['mailer_smtp_auth']
            ),
            'mailer_smtp_security' => $this->forms->choicesGenerator(
                'mailer_smtp_security',
                $mailerSmtpSecurity,
                $systemSettings['mailer_smtp_security']
            ),
            'form' => array_merge($systemSettings, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @param string $currentWysiwygEditor
     * @return array
     */
    private function fetchWysiwygEditors(string $currentWysiwygEditor): array
    {
        $wysiwyg = [];
        foreach ($this->editorRegistrar->all() as $serviceId => $editorInstance) {
            if ($editorInstance->isValid()) {
                $wysiwyg[$serviceId] = $editorInstance->getFriendlyName();
            }
        }
        return $this->forms->choicesGenerator('wysiwyg', $wysiwyg, $currentWysiwygEditor);
    }

    /**
     * @inheritdoc
     */
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }
}
