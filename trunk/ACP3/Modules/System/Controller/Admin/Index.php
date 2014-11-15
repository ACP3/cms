<?php

namespace ACP3\Modules\System\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\System;

/**
 * Class Index
 * @package ACP3\Modules\System\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var System\Model
     */
    protected $systemModel;
    /**
     * @var Core\Config
     */
    protected $systemConfig;

    /**
     * @param Core\Context\Admin $context
     * @param Core\Date $date
     * @param Core\Helpers\Secure $secureHelper
     * @param System\Model $systemModel
     * @param Core\Config $systemConfig
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        System\Model $systemModel,
        Core\Config $systemConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->systemModel = $systemModel;
        $this->systemConfig = $systemConfig;
    }

    public function actionConfiguration()
    {
        if (empty($_POST) === false) {
            $this->_configurationPost($_POST);
        }

        $this->view->assign('entries', $this->get('core.helpers.forms')->recordsPerPage(CONFIG_ENTRIES));

        // WYSIWYG-Editoren
        $editors = array_diff(scandir(CLASSES_DIR . 'WYSIWYG'), array('.', '..', 'AbstractWYSIWYG.php'));
        $wysiwyg = [];

        foreach ($editors as $editor) {
            $editor = substr($editor, 0, strrpos($editor, '.php'));
            if (!empty($editor)) {
                $wysiwyg[] = array(
                    'value' => $editor,
                    'selected' => $this->get('core.helpers.forms')->selectEntry('wysiwyg', $editor, CONFIG_WYSIWYG),
                    'lang' => $editor
                );
            }
        }
        $this->view->assign('wysiwyg', $wysiwyg);

        $this->view->assign('languages', $this->lang->getLanguages(CONFIG_LANG));

        // Zeitzonen
        $this->view->assign('time_zones', $this->date->getTimeZones(CONFIG_DATE_TIME_ZONE));

        // Wartungsmodus an/aus
        $lang_maintenance = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('maintenance', $this->get('core.helpers.forms')->selectGenerator('maintenance_mode', array(1, 0), $lang_maintenance, CONFIG_MAINTENANCE_MODE, 'checked'));

        // Robots
        $lang_robots = array(
            $this->lang->t('system', 'seo_robots_index_follow'),
            $this->lang->t('system', 'seo_robots_index_nofollow'),
            $this->lang->t('system', 'seo_robots_noindex_follow'),
            $this->lang->t('system', 'seo_robots_noindex_nofollow')
        );
        $this->view->assign('robots', $this->get('core.helpers.forms')->selectGenerator('seo_robots', array(1, 2, 3, 4), $lang_robots, CONFIG_SEO_ROBOTS));

        // Sef-URIs
        $lang_mod_rewrite = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('mod_rewrite', $this->get('core.helpers.forms')->selectGenerator('seo_mod_rewrite', array(1, 0), $lang_mod_rewrite, CONFIG_SEO_MOD_REWRITE, 'checked'));

        // Caching von Bildern
        $lang_cache_images = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('cache_images', $this->get('core.helpers.forms')->selectGenerator('cache_images', array(1, 0), $lang_cache_images, CONFIG_CACHE_IMAGES, 'checked'));

        // Mailertyp
        $lang_mailer_type = array($this->lang->t('system', 'mailer_type_php_mail'), $this->lang->t('system', 'mailer_type_smtp'));
        $this->view->assign('mailer_type', $this->get('core.helpers.forms')->selectGenerator('mailer_type', array('mail', 'smtp'), $lang_mailer_type, CONFIG_MAILER_TYPE));

        // Mailer SMTP Authentifizierung
        $lang_mailer_smtp_auth = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('mailer_smtp_auth', $this->get('core.helpers.forms')->selectGenerator('mailer_smtp_auth', array(1, 0), $lang_mailer_smtp_auth, CONFIG_MAILER_SMTP_AUTH, 'checked'));

        // Mailer SMTP Verschlüsselung
        $lang_mailer_smtp_security = array(
            $this->lang->t('system', 'mailer_smtp_security_none'),
            $this->lang->t('system', 'mailer_smtp_security_ssl'),
            $this->lang->t('system', 'mailer_smtp_security_tls')
        );
        $this->view->assign('mailer_smtp_security', $this->get('core.helpers.forms')->selectGenerator('mailer_smtp_security', array('none', 'ssl', 'tls'), $lang_mailer_smtp_security, CONFIG_MAILER_SMTP_SECURITY));

        $settings = $this->systemConfig->getSettings();

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionIndex()
    {
        return;
    }

    private function _configurationPost(array $formData)
    {
        try {
            $validator = $this->get('system.validator');
            $validator->validateSettings($formData);

            // Config aktualisieren
            $data = array(
                'cache_images' => (int)$formData['cache_images'],
                'cache_minify' => (int)$formData['cache_minify'],
                'date_format_long' => Core\Functions::strEncode($formData['date_format_long']),
                'date_format_short' => Core\Functions::strEncode($formData['date_format_short']),
                'date_time_zone' => $formData['date_time_zone'],
                'entries' => (int)$formData['entries'],
                'flood' => (int)$formData['flood'],
                'homepage' => $formData['homepage'],
                'language' => $formData['language'],
                'mailer_smtp_auth' => (int)$formData['mailer_smtp_auth'],
                'mailer_smtp_host' => $formData['mailer_smtp_host'],
                'mailer_smtp_password' => $formData['mailer_smtp_password'],
                'mailer_smtp_port' => (int)$formData['mailer_smtp_port'],
                'mailer_smtp_security' => $formData['mailer_smtp_security'],
                'mailer_smtp_user' => $formData['mailer_smtp_user'],
                'mailer_type' => $formData['mailer_type'],
                'maintenance_message' => $formData['maintenance_message'],
                'maintenance_mode' => (int)$formData['maintenance_mode'],
                'seo_meta_description' => Core\Functions::strEncode($formData['seo_meta_description']),
                'seo_meta_keywords' => Core\Functions::strEncode($formData['seo_meta_keywords']),
                'seo_mod_rewrite' => (int)$formData['seo_mod_rewrite'],
                'seo_robots' => (int)$formData['seo_robots'],
                'seo_title' => Core\Functions::strEncode($formData['seo_title']),
                'wysiwyg' => $formData['wysiwyg']
            );

            $bool = $this->systemConfig->setSettings($data);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'config_edit_success' : 'config_edit_error'), 'acp/system/index/configuration');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/system/index/configuration');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}