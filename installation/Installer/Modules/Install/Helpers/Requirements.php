<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Helpers;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Filesystem;
use ACP3\Installer\Core\I18n\Translator;

/**
 * Class Requirements
 * @package ACP3\Installer\Modules\Install\Helpers
 */
class Requirements
{
    const REQUIRED_PHP_VERSION = '5.5.0';
    const COLOR_ERROR = 'f00';
    const COLOR_SUCCESS = '090';
    const CLASS_ERROR = 'danger';
    const CLASS_SUCCESS = 'success';
    const CLASS_WARNING = 'warning';

    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Installer\Core\I18n\Translator
     */
    protected $translator;

    /**
     * Requirements constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Installer\Core\I18n\Translator   $translator
     */
    public function __construct(
        ApplicationPath $appPath,
        Translator $translator
    ) {
        $this->appPath = $appPath;
        $this->translator = $translator;
    }

    /**
     * Checks, whether the mandatory system requirements of the ACP3 are fulfilled
     * @return array
     */
    public function checkMandatoryRequirements()
    {
        $requirements = [];
        $requirements[0]['name'] = $this->translator->t('install', 'php_version');
        $requirements[0]['color'] = version_compare(phpversion(), self::REQUIRED_PHP_VERSION, '>=') ? self::COLOR_SUCCESS : self::COLOR_ERROR;
        $requirements[0]['found'] = phpversion();
        $requirements[0]['required'] = self::REQUIRED_PHP_VERSION;
        $requirements[1]['name'] = $this->translator->t('install', 'pdo_extension');
        $requirements[1]['color'] = extension_loaded('pdo') && extension_loaded('pdo_mysql') ? self::COLOR_SUCCESS : self::COLOR_ERROR;
        $requirements[1]['found'] = $this->translator->t('install',
            $requirements[1]['color'] == self::COLOR_SUCCESS ? 'on' : 'off');
        $requirements[1]['required'] = $this->translator->t('install', 'on');
        $requirements[2]['name'] = $this->translator->t('install', 'gd_library');
        $requirements[2]['color'] = extension_loaded('gd') ? self::COLOR_SUCCESS : self::COLOR_ERROR;
        $requirements[2]['found'] = $this->translator->t('install',
            $requirements[2]['color'] == self::COLOR_SUCCESS ? 'on' : 'off');
        $requirements[2]['required'] = $this->translator->t('install', 'on');
        $requirements[3]['name'] = $this->translator->t('install', 'register_globals');
        $requirements[3]['color'] = ((bool)ini_get('register_globals')) ? self::COLOR_ERROR : self::COLOR_SUCCESS;
        $requirements[3]['found'] = $this->translator->t('install', ((bool)ini_get('register_globals')) ? 'on' : 'off');
        $requirements[3]['required'] = $this->translator->t('install', 'off');
        $requirements[4]['name'] = $this->translator->t('install', 'safe_mode');
        $requirements[4]['color'] = ((bool)ini_get('safe_mode')) ? self::COLOR_ERROR : self::COLOR_SUCCESS;
        $requirements[4]['found'] = $this->translator->t('install', ((bool)ini_get('safe_mode')) ? 'on' : 'off');
        $requirements[4]['required'] = $this->translator->t('install', 'off');

        $stopInstall = false;
        foreach ($requirements as $requirement) {
            if ($requirement['color'] !== self::COLOR_SUCCESS) {
                $stopInstall = true;
            }
        }

        return [
            $requirements,
            $stopInstall
        ];
    }

    /**
     * Checks, whether all mandatory files and folders exist and have the correct permissions set
     *
     * @return array
     */
    public function checkFolderAndFilePermissions()
    {
        $defaults = $this->fetchRequiredFilesAndDirectories();
        $requiredFilesAndDirs = [];
        $checkAgain = false;

        $i = 0;
        foreach ($defaults as $fileOrDirectory) {
            $requiredFilesAndDirs[$i] = $this->requiredFileOrFolderHasPermission($fileOrDirectory);

            if ($requiredFilesAndDirs[$i]['class_1'] === self::CLASS_ERROR || $requiredFilesAndDirs[$i]['class_2'] === self::CLASS_ERROR) {
                $checkAgain = true;
            }
            ++$i;
        }
        return [$requiredFilesAndDirs, $checkAgain];
    }

    /**
     * @return array
     */
    public function checkOptionalRequirements()
    {
        return [
            [
                'setting' => $this->translator->t('install', 'maximum_uploadsize'),
                'class' => ini_get('post_max_size') > 0 ? self::CLASS_SUCCESS : self::CLASS_WARNING,
                'value' => ini_get('post_max_size'),
            ],
            [
                'setting' => $this->translator->t('install', 'magic_quotes'),
                'class' => (bool)ini_get('magic_quotes_gpc') ? self::CLASS_WARNING : self::CLASS_SUCCESS,
                'value' => $this->translator->t('install', (bool)ini_get('magic_quotes_gpc') ? 'on' : 'off'),
            ]
        ];
    }

    /**
     * @param string $fileOrDirectory
     *
     * @return array
     */
    private function requiredFileOrFolderHasPermission($fileOrDirectory)
    {
        $result = [];
        $result['path'] = $fileOrDirectory;
        // Überprüfen, ob es eine Datei oder ein Ordner ist
        if (is_file(ACP3_ROOT_DIR . $fileOrDirectory) === true) {
            $result['class_1'] = self::CLASS_SUCCESS;
            $result['exists'] = $this->translator->t('install', 'found');
        } elseif (is_dir(ACP3_ROOT_DIR . $fileOrDirectory) === true) {
            $result['class_1'] = self::CLASS_SUCCESS;
            $result['exists'] = $this->translator->t('install', 'found');
        } else {
            $result['class_1'] = self::CLASS_ERROR;
            $result['exists'] = $this->translator->t('install', 'not_found');
        }
        $result['class_2'] = is_writable(ACP3_ROOT_DIR . $fileOrDirectory) === true ? self::CLASS_SUCCESS : self::CLASS_ERROR;
        $result['writable'] = $result['class_2'] === self::CLASS_SUCCESS ? $this->translator->t('install',
            'writable') : $this->translator->t('install', 'not_writable');

        return $result;
    }

    /**
     * @return array
     */
    private function fetchRequiredFilesAndDirectories()
    {
        $defaults = ['ACP3/config.yml', 'cache/'];

        foreach (Filesystem::scandir($this->appPath->getModulesDir()) as $row) {
            $path = 'uploads/' . $row . '/';
            if (is_dir(ACP3_ROOT_DIR . $path) === true) {
                $defaults[] = $path;
            }
        }
        return $defaults;
    }
}
