<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Helpers;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\I18n\Translator;

class Requirements
{
    private const REQUIRED_PHP_VERSION = '7.1.0';
    private const COLOR_ERROR = 'f00';
    private const COLOR_SUCCESS = '090';
    private const CLASS_ERROR = 'danger';
    private const CLASS_SUCCESS = 'success';
    private const CLASS_WARNING = 'warning';

    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;

    /**
     * Requirements constructor.
     */
    public function __construct(
        ApplicationPath $appPath,
        Translator $translator
    ) {
        $this->appPath = $appPath;
        $this->translator = $translator;
    }

    /**
     * Checks, whether the mandatory system requirements of the ACP3 are fulfilled.
     *
     * @return array
     */
    public function checkMandatoryRequirements()
    {
        $requirements = [];
        $requirements[0]['name'] = $this->translator->t('installer', 'php_version');
        $requirements[0]['color'] = \version_compare(PHP_VERSION, self::REQUIRED_PHP_VERSION, '>=') ? self::COLOR_SUCCESS : self::COLOR_ERROR;
        $requirements[0]['found'] = PHP_VERSION;
        $requirements[0]['required'] = self::REQUIRED_PHP_VERSION;
        $requirements[1]['name'] = $this->translator->t('installer', 'pdo_extension');
        $requirements[1]['color'] = \extension_loaded('pdo') && \extension_loaded('pdo_mysql') ? self::COLOR_SUCCESS : self::COLOR_ERROR;
        $requirements[1]['found'] = $this->translator->t(
            'installer',
            $requirements[1]['color'] === self::COLOR_SUCCESS ? 'on' : 'off'
        );
        $requirements[1]['required'] = $this->translator->t('installer', 'on');
        $requirements[2]['name'] = $this->translator->t('installer', 'gd_library');
        $requirements[2]['color'] = \extension_loaded('gd') ? self::COLOR_SUCCESS : self::COLOR_ERROR;
        $requirements[2]['found'] = $this->translator->t(
            'installer',
            $requirements[2]['color'] === self::COLOR_SUCCESS ? 'on' : 'off'
        );
        $requirements[2]['required'] = $this->translator->t('installer', 'on');

        $stopInstall = false;
        foreach ($requirements as $requirement) {
            if ($requirement['color'] !== self::COLOR_SUCCESS) {
                $stopInstall = true;
            }
        }

        return [
            $requirements,
            $stopInstall,
        ];
    }

    /**
     * Checks, whether all mandatory files and folders exist and have the correct permissions set.
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
                'setting' => $this->translator->t('installer', 'maximum_uploadsize'),
                'class' => \ini_get('post_max_size') > 0 ? self::CLASS_SUCCESS : self::CLASS_WARNING,
                'value' => \ini_get('post_max_size'),
            ],
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
        if (\is_file(ACP3_ROOT_DIR . DIRECTORY_SEPARATOR . $fileOrDirectory) === true) {
            $result['class_1'] = self::CLASS_SUCCESS;
            $result['exists'] = $this->translator->t('installer', 'found');
        } elseif (\is_dir(ACP3_ROOT_DIR . DIRECTORY_SEPARATOR . $fileOrDirectory) === true) {
            $result['class_1'] = self::CLASS_SUCCESS;
            $result['exists'] = $this->translator->t('installer', 'found');
        } else {
            $result['class_1'] = self::CLASS_ERROR;
            $result['exists'] = $this->translator->t('installer', 'not_found');
        }
        $result['class_2'] = \is_writable(ACP3_ROOT_DIR . DIRECTORY_SEPARATOR . $fileOrDirectory) === true ? self::CLASS_SUCCESS : self::CLASS_ERROR;
        $result['writable'] = $result['class_2'] === self::CLASS_SUCCESS ? $this->translator->t(
            'installer',
            'writable'
        ) : $this->translator->t('installer', 'not_writable');

        return $result;
    }

    private function fetchRequiredFilesAndDirectories(): array
    {
        return ['/ACP3/config.yml', '/cache/', '/uploads/', '/uploads/assets/'];
    }
}
