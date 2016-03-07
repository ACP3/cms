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
class Configuration extends Core\Controller\AdminAction
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
     * @param \ACP3\Core\Controller\Context\AdminContext                       $context
     * @param \ACP3\Core\Helpers\Forms                                         $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                     $formTokenHelper
     * @param \ACP3\Modules\ACP3\System\Validation\AdminSettingsFormValidation $systemValidator
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
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
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all());
        }

        $systemSettings = $this->config->getSettings('system');

        // WYSIWYG editors
        $services = $this->container->getServiceIds();
        $wysiwyg = [];
        foreach ($services as $service) {
            if (strpos($service, 'core.wysiwyg') === 0) {
                $editor = $this->container->get($service);

                if ($editor instanceof Core\WYSIWYG\AbstractWYSIWYG) {
                    $wysiwyg[] = [
                        'value' => $service,
                        'selected' => $this->formsHelper->selectEntry('wysiwyg', $service, $systemSettings['wysiwyg']),
                        'lang' => $editor->getFriendlyName()
                    ];
                }
            }
        }

        // Mailertyp
        $mailerTypes = [
            'mail' => $this->translator->t('system', 'mailer_type_php_mail'),
            'smtp' => $this->translator->t('system', 'mailer_type_smtp')
        ];

        // Mailer SMTP Verschlüsselung
        $mailerSmtpSecurity = [
            'none' => $this->translator->t('system', 'mailer_smtp_security_none'),
            'ssl' => $this->translator->t('system', 'mailer_smtp_security_ssl'),
            'tls' => $this->translator->t('system', 'mailer_smtp_security_tls')
        ];

        return [
            'entries' => $this->formsHelper->recordsPerPage($systemSettings['entries']),
            'wysiwyg' => $wysiwyg,
            'languages' => $this->translator->getLanguagePack($systemSettings['lang']),
            'time_zones' => $this->get('core.helpers.date')->getTimeZones($systemSettings['date_time_zone']),
            'maintenance' => $this->formsHelper->yesNoCheckboxGenerator('maintenance_mode', $systemSettings['maintenance_mode']),
            'cache_images' => $this->formsHelper->yesNoCheckboxGenerator('cache_images', $systemSettings['cache_images']),
            'mailer_type' => $this->formsHelper->selectGenerator('mailer_type', $mailerTypes, $systemSettings['mailer_type']),
            'mailer_smtp_auth' => $this->formsHelper->yesNoCheckboxGenerator('mailer_smtp_auth', $systemSettings['mailer_smtp_auth']),
            'mailer_smtp_security' => $this->formsHelper->selectGenerator('mailer_smtp_security', $mailerSmtpSecurity, $systemSettings['mailer_smtp_security']),
            'form' => array_merge($systemSettings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $this->systemValidator->validate($formData);

                // Update the system config
                $data = [
                    'cache_images' => (int)$formData['cache_images'],
                    'cache_minify' => (int)$formData['cache_minify'],
                    'date_format_long' => $this->get('core.helpers.secure')->strEncode($formData['date_format_long']),
                    'date_format_short' => $this->get('core.helpers.secure')->strEncode($formData['date_format_short']),
                    'date_time_zone' => $formData['date_time_zone'],
                    'entries' => (int)$formData['entries'],
                    'flood' => (int)$formData['flood'],
                    'homepage' => $formData['homepage'],
                    'lang' => $formData['language'],
                    'mailer_smtp_auth' => (int)$formData['mailer_smtp_auth'],
                    'mailer_smtp_host' => $formData['mailer_smtp_host'],
                    'mailer_smtp_password' => $formData['mailer_smtp_password'],
                    'mailer_smtp_port' => (int)$formData['mailer_smtp_port'],
                    'mailer_smtp_security' => $formData['mailer_smtp_security'],
                    'mailer_smtp_user' => $formData['mailer_smtp_user'],
                    'mailer_type' => $formData['mailer_type'],
                    'maintenance_message' => $formData['maintenance_message'],
                    'maintenance_mode' => (int)$formData['maintenance_mode'],
                    'wysiwyg' => $formData['wysiwyg']
                ];

                $bool = $this->config->setSettings($data, 'system');

                $this->formTokenHelper->unsetFormToken();

                return $this->redirectMessages()->setMessage($bool,
                    $this->translator->t('system', $bool === true ? 'config_edit_success' : 'config_edit_error'),
                    $this->request->getFullPath());
            },
            $this->request->getFullPath()
        );
    }
}
