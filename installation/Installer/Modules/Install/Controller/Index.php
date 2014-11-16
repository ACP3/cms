<?php

namespace ACP3\Installer\Modules\Install\Controller;

use ACP3\Installer\Core;

/**
 * Class Index
 * @package ACP3\Installer\Modules\Install\Controller
 */
class Index extends AbstractController
{
    const REQUIRED_PHP_VERSION = '5.3.2';
    const COLOR_ERROR = 'f00';
    const COLOR_SUCCESS = '090';
    const CLASS_ERROR = 'danger';
    const CLASS_SUCCESS = 'success';
    const CLASS_WARNING = 'warning';


    public function actionRequirements()
    {
        // Allgemeine Voraussetzungen
        $requirements = array();
        $requirements[0]['name'] = $this->lang->t('install', 'php_version');
        $requirements[0]['color'] = version_compare(phpversion(), self::REQUIRED_PHP_VERSION, '>=') ? self::COLOR_SUCCESS : self::COLOR_ERROR;
        $requirements[0]['found'] = phpversion();
        $requirements[0]['required'] = self::REQUIRED_PHP_VERSION;
        $requirements[1]['name'] = $this->lang->t('install', 'pdo_extension');
        $requirements[1]['color'] = extension_loaded('pdo') && extension_loaded('pdo_mysql') ? self::COLOR_SUCCESS : self::COLOR_ERROR;
        $requirements[1]['found'] = $this->lang->t('install', $requirements[1]['color'] == self::COLOR_SUCCESS ? 'on' : 'off');
        $requirements[1]['required'] = $this->lang->t('install', 'on');
        $requirements[2]['name'] = $this->lang->t('install', 'gd_library');
        $requirements[2]['color'] = extension_loaded('gd') ? self::COLOR_SUCCESS : self::COLOR_ERROR;
        $requirements[2]['found'] = $this->lang->t('install', $requirements[2]['color'] == self::COLOR_SUCCESS ? 'on' : 'off');
        $requirements[2]['required'] = $this->lang->t('install', 'on');
        $requirements[3]['name'] = $this->lang->t('install', 'register_globals');
        $requirements[3]['color'] = ((bool)ini_get('register_globals')) ? self::COLOR_ERROR : self::COLOR_SUCCESS;
        $requirements[3]['found'] = $this->lang->t('install', ((bool)ini_get('register_globals')) ? 'on' : 'off');
        $requirements[3]['required'] = $this->lang->t('install', 'off');
        $requirements[4]['name'] = $this->lang->t('install', 'safe_mode');
        $requirements[4]['color'] = ((bool)ini_get('safe_mode')) ? self::COLOR_ERROR : self::COLOR_SUCCESS;
        $requirements[4]['found'] = $this->lang->t('install', ((bool)ini_get('safe_mode')) ? 'on' : 'off');
        $requirements[4]['required'] = $this->lang->t('install', 'off');

        $this->view->assign('requirements', $requirements);

        $defaults = array('ACP3/config/config.yml');

        // Uploadordner
        $uploads = array_diff(scandir(UPLOADS_DIR), array('.', '..'));
        foreach ($uploads as $row) {
            $path = 'uploads/' . $row . '/';
            if (is_dir(ACP3_ROOT_DIR . $path) === true) {
                $defaults[] = $path;
            }
        }
        $requiredFilesAndDirs = array();
        $checkAgain = false;

        $i = 0;
        foreach ($defaults as $row) {
            $requiredFilesAndDirs[$i]['path'] = $row;
            // Überprüfen, ob es eine Datei oder ein Ordner ist
            if (is_file(ACP3_ROOT_DIR . $row) === true) {
                $requiredFilesAndDirs[$i]['class_1'] = self::CLASS_SUCCESS;
                $requiredFilesAndDirs[$i]['exists'] = $this->lang->t('install', 'found');
            } elseif (is_dir(ACP3_ROOT_DIR . $row) === true) {
                $requiredFilesAndDirs[$i]['class_1'] = self::CLASS_SUCCESS;
                $requiredFilesAndDirs[$i]['exists'] = $this->lang->t('install', 'found');
            } else {
                $requiredFilesAndDirs[$i]['class_1'] = self::CLASS_ERROR;
                $requiredFilesAndDirs[$i]['exists'] = $this->lang->t('install', 'not_found');
            }
            $requiredFilesAndDirs[$i]['class_2'] = is_writable(ACP3_ROOT_DIR . $row) === true ? self::CLASS_SUCCESS : self::CLASS_ERROR;
            $requiredFilesAndDirs[$i]['writable'] = $requiredFilesAndDirs[$i]['class_2'] === self::CLASS_SUCCESS ? $this->lang->t('install', 'writable') : $this->lang->t('install', 'not_writable');
            if ($requiredFilesAndDirs[$i]['class_1'] == self::CLASS_ERROR || $requiredFilesAndDirs[$i]['class_2'] == self::CLASS_ERROR) {
                $checkAgain = true;
            }
            ++$i;
        }
        $this->view->assign('files_dirs', $requiredFilesAndDirs);

        // PHP Einstellungen
        $phpSettings = array();
        $phpSettings[0]['setting'] = $this->lang->t('install', 'maximum_uploadsize');
        $phpSettings[0]['class'] = ini_get('post_max_size') > 0 ? self::CLASS_SUCCESS : self::CLASS_WARNING;
        $phpSettings[0]['value'] = ini_get('post_max_size');
        $phpSettings[1]['setting'] = $this->lang->t('install', 'magic_quotes');
        $phpSettings[1]['class'] = (bool)ini_get('magic_quotes_gpc') ? self::CLASS_WARNING : self::CLASS_SUCCESS;
        $phpSettings[1]['value'] = $this->lang->t('install', (bool)ini_get('magic_quotes_gpc') ? 'on' : 'off');
        $this->view->assign('php_settings', $phpSettings);

        foreach ($requirements as $row) {
            if ($row['color'] !== self::COLOR_SUCCESS) {
                $this->view->assign('stop_install', true);
            }
        }

        if ($checkAgain === true) {
            $this->view->assign('check_again', true);
        }
    }

    public function actionLicence()
    {
        return;
    }

    public function actionIndex()
    {
        return;
    }

}
