<?php

namespace ACP3\Modules\ACP3\System\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\System;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\System\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\System\Model\ModuleRepository
     */
    protected $systemModuleRepository;
    /**
     * @var \ACP3\Modules\ACP3\System\Validation\Settings
     */
    protected $systemValidator;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext       $context
     * @param \ACP3\Core\Date                                  $date
     * @param \ACP3\Core\Helpers\FormToken                     $formTokenHelper
     * @param \ACP3\Modules\ACP3\System\Model\ModuleRepository $systemModuleRepository
     * @param \ACP3\Modules\ACP3\System\Validation\Settings    $systemValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        System\Model\ModuleRepository $systemModuleRepository,
        System\Validation\Settings $systemValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->systemValidator = $systemValidator;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionConfiguration()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_configurationPost($this->request->getPost()->all());
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
                        'selected' => $this->get('core.helpers.forms')->selectEntry('wysiwyg', $service, $systemSettings['wysiwyg']),
                        'lang' => $editor->getFriendlyName()
                    ];
                }
            }
        }

        // Mailertyp
        $lang_mailerType = [
            $this->translator->t('system', 'mailer_type_php_mail'),
            $this->translator->t('system', 'mailer_type_smtp')
        ];

        // Mailer SMTP Verschlüsselung
        $lang_mailerSmtpSecurity = [
            $this->translator->t('system', 'mailer_smtp_security_none'),
            $this->translator->t('system', 'mailer_smtp_security_ssl'),
            $this->translator->t('system', 'mailer_smtp_security_tls')
        ];

        $this->formTokenHelper->generateFormToken();

        return [
            'entries' => $this->get('core.helpers.forms')->recordsPerPage($systemSettings['entries']),
            'wysiwyg' => $wysiwyg,
            'languages' => $this->translator->getLanguagePack($systemSettings['lang']),
            'time_zones' => $this->get('core.helpers.date')->getTimeZones($systemSettings['date_time_zone']),
            'maintenance' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('maintenance_mode', $systemSettings['maintenance_mode']),
            'cache_images' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('cache_images', $systemSettings['cache_images']),
            'mailer_type' => $this->get('core.helpers.forms')->selectGenerator('mailer_type', ['mail', 'smtp'], $lang_mailerType, $systemSettings['mailer_type']),
            'mailer_smtp_auth' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('mailer_smtp_auth', $systemSettings['mailer_smtp_auth']),
            'mailer_smtp_security' => $this->get('core.helpers.forms')->selectGenerator('mailer_smtp_security', ['none', 'ssl', 'tls'], $lang_mailerSmtpSecurity, $systemSettings['mailer_smtp_security']),
            'form' => array_merge($systemSettings, $this->request->getPost()->all())
        ];
    }

    public function actionIndex()
    {
        return;
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _configurationPost(array $formData)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $this->systemValidator->validate($formData);

                // Update the system config
                $data = [
                    'cache_images' => (int)$formData['cache_images'],
                    'cache_minify' => (int)$formData['cache_minify'],
                    'date_format_long' => Core\Functions::strEncode($formData['date_format_long']),
                    'date_format_short' => Core\Functions::strEncode($formData['date_format_short']),
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
