<?php

namespace ACP3\Modules\ACP3\System\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\System;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\System\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\System\Model
     */
    protected $systemModel;
    /**
     * @var \ACP3\Modules\ACP3\System\Validator
     */
    protected $systemValidator;

    /**
     * @param \ACP3\Core\Context\Admin       $context
     * @param \ACP3\Core\Date                $date
     * @param \ACP3\Core\Helpers\Secure      $secureHelper
     * @param \ACP3\Modules\ACP3\System\Model     $systemModel
     * @param \ACP3\Modules\ACP3\System\Validator $systemValidator
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        System\Model $systemModel,
        System\Validator $systemValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->systemModel = $systemModel;
        $this->systemValidator = $systemValidator;
    }

    public function actionConfiguration()
    {
        if (empty($_POST) === false) {
            $this->_configurationPost($_POST);
        }

        $systemSettings = $this->config->getSettings('system');

        $this->view->assign('entries', $this->get('core.helpers.forms')->recordsPerPage($systemSettings['entries']));

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
        $this->view->assign('wysiwyg', $wysiwyg);

        $this->view->assign('languages', $this->lang->getLanguages($systemSettings['lang']));

        // Zeitzonen
        $this->view->assign('time_zones', $this->date->getTimeZones($systemSettings['date_time_zone']));

        // Wartungsmodus an/aus
        $lang_maintenance = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('maintenance', $this->get('core.helpers.forms')->selectGenerator('maintenance_mode', [1, 0], $lang_maintenance, $systemSettings['maintenance_mode'], 'checked'));

        // Caching von Bildern
        $lang_cacheImages = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('cache_images', $this->get('core.helpers.forms')->selectGenerator('cache_images', [1, 0], $lang_cacheImages, $systemSettings['cache_images'], 'checked'));

        // Mailertyp
        $lang_mailerType = [$this->lang->t('system', 'mailer_type_php_mail'), $this->lang->t('system', 'mailer_type_smtp')];
        $this->view->assign('mailer_type', $this->get('core.helpers.forms')->selectGenerator('mailer_type', ['mail', 'smtp'], $lang_mailerType, $systemSettings['mailer_type']));

        // Mailer SMTP Authentifizierung
        $lang_mailerSmtpAuth = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('mailer_smtp_auth', $this->get('core.helpers.forms')->selectGenerator('mailer_smtp_auth', [1, 0], $lang_mailerSmtpAuth, $systemSettings['mailer_smtp_auth'], 'checked'));

        // Mailer SMTP Verschlüsselung
        $lang_mailerSmtpSecurity = [
            $this->lang->t('system', 'mailer_smtp_security_none'),
            $this->lang->t('system', 'mailer_smtp_security_ssl'),
            $this->lang->t('system', 'mailer_smtp_security_tls')
        ];
        $this->view->assign('mailer_smtp_security', $this->get('core.helpers.forms')->selectGenerator('mailer_smtp_security', ['none', 'ssl', 'tls'], $lang_mailerSmtpSecurity, $systemSettings['mailer_smtp_security']));

        $this->view->assign('form', array_merge($systemSettings, $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionIndex()
    {
        return;
    }

    /**
     * @param array $formData
     */
    protected function _configurationPost(array $formData)
    {
        try {
            $this->systemValidator->validateSettings($formData);

            // Config aktualisieren
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

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'config_edit_success' : 'config_edit_error'), 'acp/system/index/configuration');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/system/index/configuration');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
