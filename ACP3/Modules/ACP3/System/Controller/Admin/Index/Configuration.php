<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\System;

/**
 * Class Configuration
 * @package ACP3\Modules\ACP3\System\Controller\Admin\Index
 */
class Configuration extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\System\Validation\AdminSettingsFormValidation
     */
    protected $systemValidator;

    /**
     * Configuration constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                       $context
     * @param \ACP3\Core\Helpers\Forms                                         $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                     $formTokenHelper
     * @param \ACP3\Modules\ACP3\System\Validation\AdminSettingsFormValidation $systemValidator
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        System\Validation\AdminSettingsFormValidation $systemValidator
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->systemValidator = $systemValidator;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $systemSettings = $this->config->getSettings(System\Installer\Schema::MODULE_NAME);

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
            'entries' => $this->formsHelper->recordsPerPage($systemSettings['entries']),
            'wysiwyg' => $this->fetchWysiwygEditors($systemSettings),
            'languages' => $this->translator->getLanguagePack($systemSettings['lang']),
            'mod_rewrite' => $this->formsHelper->yesNoCheckboxGenerator('mod_rewrite', $systemSettings['mod_rewrite']),
            'time_zones' => $this->get('core.helpers.date')->getTimeZones($systemSettings['date_time_zone']),
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
            'form' => array_merge($systemSettings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->systemValidator->validate($formData);

                $data = [
                    'cache_images' => (int)$formData['cache_images'],
                    'cache_lifetime' => (int)$formData['cache_lifetime'],
                    'date_format_long' => $this->get('core.helpers.secure')->strEncode($formData['date_format_long']),
                    'date_format_short' => $this->get('core.helpers.secure')->strEncode($formData['date_format_short']),
                    'date_time_zone' => $formData['date_time_zone'],
                    'entries' => (int)$formData['entries'],
                    'flood' => (int)$formData['flood'],
                    'homepage' => $formData['homepage'],
                    'lang' => $formData['language'],
                    'mod_rewrite' => (int)$formData['mod_rewrite'],
                    'mailer_smtp_auth' => (int)$formData['mailer_smtp_auth'],
                    'mailer_smtp_host' => $formData['mailer_smtp_host'],
                    'mailer_smtp_password' => $formData['mailer_smtp_password'],
                    'mailer_smtp_port' => (int)$formData['mailer_smtp_port'],
                    'mailer_smtp_security' => $formData['mailer_smtp_security'],
                    'mailer_smtp_user' => $formData['mailer_smtp_user'],
                    'mailer_type' => $formData['mailer_type'],
                    'maintenance_message' => $formData['maintenance_message'],
                    'maintenance_mode' => (int)$formData['maintenance_mode'],
                    'page_cache_is_enabled' => (int)$formData['page_cache_is_enabled'],
                    'page_cache_purge_mode' => (int)$formData['page_cache_purge_mode'],
                    'site_title' => $this->get('core.helpers.secure')->strEncode($formData['site_title']),
                    'wysiwyg' => $formData['wysiwyg']
                ];

                $bool = $this->config->saveSettings($data, System\Installer\Schema::MODULE_NAME);

                return $this->redirectMessages()->setMessage(
                    $bool,
                    $this->translator->t('system', $bool === true ? 'config_edit_success' : 'config_edit_error'),
                    $this->request->getFullPath()
                );
            },
            $this->request->getFullPath()
        );
    }

    /**
     * @param $systemSettings
     * @return array
     */
    protected function fetchWysiwygEditors($systemSettings)
    {
        $services = $this->get('core.wysiwyg.wysiwyg_factory')->getWysiwygEditors();
        $wysiwyg = [];
        foreach ($services as $serviceId => $editorInstance) {
            /** @var \ACP3\Core\WYSIWYG\AbstractWYSIWYG $editorInstance */
            $wysiwyg[$serviceId] =  $editorInstance->getFriendlyName();
        }
        return $this->formsHelper->choicesGenerator('wysiwyg', $wysiwyg, $systemSettings['wysiwyg']);
    }
}
