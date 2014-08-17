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
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var System\Model
     */
    protected $systemModel;

    public function __construct(
        Core\Context\Admin $context,
        \Doctrine\DBAL\Connection $db,
        Core\Helpers\Secure $secureHelper,
        System\Model $systemModel)
    {
        parent::__construct($context);

        $this->db = $db;
        $this->secureHelper = $secureHelper;
        $this->systemModel = $systemModel;
    }

    public function actionConfiguration()
    {
        $config = new Core\Config($this->db, 'system');

        $redirect = $this->redirectMessages();

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('system.validator');
                $validator->validateSettings($_POST);

                // Config aktualisieren
                $data = array(
                    'cache_images' => (int)$_POST['cache_images'],
                    'cache_minify' => (int)$_POST['cache_minify'],
                    'date_format_long' => Core\Functions::strEncode($_POST['date_format_long']),
                    'date_format_short' => Core\Functions::strEncode($_POST['date_format_short']),
                    'date_time_zone' => $_POST['date_time_zone'],
                    'entries' => (int)$_POST['entries'],
                    'extra_css' => $_POST['extra_css'],
                    'extra_js' => $_POST['extra_js'],
                    'flood' => (int)$_POST['flood'],
                    'homepage' => $_POST['homepage'],
                    'icons_path' => $_POST['icons_path'],
                    'language' => $_POST['language'],
                    'mailer_smtp_auth' => (int)$_POST['mailer_smtp_auth'],
                    'mailer_smtp_host' => $_POST['mailer_smtp_host'],
                    'mailer_smtp_password' => $_POST['mailer_smtp_password'],
                    'mailer_smtp_port' => (int)$_POST['mailer_smtp_port'],
                    'mailer_smtp_security' => $_POST['mailer_smtp_security'],
                    'mailer_smtp_user' => $_POST['mailer_smtp_user'],
                    'mailer_type' => $_POST['mailer_type'],
                    'maintenance_message' => $_POST['maintenance_message'],
                    'maintenance_mode' => (int)$_POST['maintenance_mode'],
                    'seo_meta_description' => Core\Functions::strEncode($_POST['seo_meta_description']),
                    'seo_meta_keywords' => Core\Functions::strEncode($_POST['seo_meta_keywords']),
                    'seo_mod_rewrite' => (int)$_POST['seo_mod_rewrite'],
                    'seo_robots' => (int)$_POST['seo_robots'],
                    'seo_title' => Core\Functions::strEncode($_POST['seo_title']),
                    'wysiwyg' => $_POST['wysiwyg']
                );

                $bool = $config->setSettings($data);

                // Gecachete Stylesheets und JavaScript Dateien löschen
                if (CONFIG_EXTRA_CSS !== $_POST['extra_css'] ||
                    CONFIG_EXTRA_JS !== $_POST['extra_js']
                ) {
                    Core\Cache::purge(UPLOADS_DIR . 'cache/minify');
                }

                $this->secureHelper->unsetFormToken($this->request->query);

                $redirect->setMessage($bool, $this->lang->t('system', $bool === true ? 'config_edit_success' : 'config_edit_error'), 'acp/system/index/configuration');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $redirect->setMessage(false, $e->getMessage(), 'acp/system/index/configuration');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            }
        }

        $redirect->getMessage();

        $this->view->assign('entries', Core\Functions::recordsPerPage(CONFIG_ENTRIES));

        // WYSIWYG-Editoren
        $editors = array_diff(scandir(CLASSES_DIR . 'WYSIWYG'), array('.', '..', 'AbstractWYSIWYG.php'));
        $c_editors = count($editors);
        $wysiwyg = array();

        for ($i = 0; $i < $c_editors; ++$i) {
            $editors[$i] = substr($editors[$i], 0, strrpos($editors[$i], '.php'));
            if (!empty($editors[$i])) {
                $wysiwyg[$i]['value'] = $editors[$i];
                $wysiwyg[$i]['selected'] = Core\Functions::selectEntry('wysiwyg', $editors[$i], CONFIG_WYSIWYG);
                $wysiwyg[$i]['lang'] = $editors[$i];
            }
        }
        $this->view->assign('wysiwyg', $wysiwyg);

        $this->view->assign('languages', $this->lang->getLanguages(CONFIG_LANG));

        // Zeitzonen
        $this->view->assign('time_zones', Core\Date::getTimeZones(CONFIG_DATE_TIME_ZONE));

        // Wartungsmodus an/aus
        $lang_maintenance = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('maintenance', Core\Functions::selectGenerator('maintenance_mode', array(1, 0), $lang_maintenance, CONFIG_MAINTENANCE_MODE, 'checked'));

        // Robots
        $lang_robots = array(
            $this->lang->t('system', 'seo_robots_index_follow'),
            $this->lang->t('system', 'seo_robots_index_nofollow'),
            $this->lang->t('system', 'seo_robots_noindex_follow'),
            $this->lang->t('system', 'seo_robots_noindex_nofollow')
        );
        $this->view->assign('robots', Core\Functions::selectGenerator('seo_robots', array(1, 2, 3, 4), $lang_robots, CONFIG_SEO_ROBOTS));

        // Sef-URIs
        $lang_mod_rewrite = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('mod_rewrite', Core\Functions::selectGenerator('seo_mod_rewrite', array(1, 0), $lang_mod_rewrite, CONFIG_SEO_MOD_REWRITE, 'checked'));

        // Caching von Bildern
        $lang_cache_images = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('cache_images', Core\Functions::selectGenerator('cache_images', array(1, 0), $lang_cache_images, CONFIG_CACHE_IMAGES, 'checked'));

        // Mailertyp
        $lang_mailer_type = array($this->lang->t('system', 'mailer_type_php_mail'), $this->lang->t('system', 'mailer_type_smtp'));
        $this->view->assign('mailer_type', Core\Functions::selectGenerator('mailer_type', array('mail', 'smtp'), $lang_mailer_type, CONFIG_MAILER_TYPE));

        // Mailer SMTP Authentifizierung
        $lang_mailer_smtp_auth = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('mailer_smtp_auth', Core\Functions::selectGenerator('mailer_smtp_auth', array(1, 0), $lang_mailer_smtp_auth, CONFIG_MAILER_SMTP_AUTH, 'checked'));

        // Mailer SMTP Verschlüsselung
        $lang_mailer_smtp_security = array(
            $this->lang->t('system', 'mailer_smtp_security_none'),
            $this->lang->t('system', 'mailer_smtp_security_ssl'),
            $this->lang->t('system', 'mailer_smtp_security_tls')
        );
        $this->view->assign('mailer_smtp_security', Core\Functions::selectGenerator('mailer_smtp_security', array('none', 'ssl', 'tls'), $lang_mailer_smtp_security, CONFIG_MAILER_SMTP_SECURITY));

        $settings = $config->getSettings();

        $this->view->assign('form', array_merge($settings, $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionIndex()
    {
        return;
    }
}