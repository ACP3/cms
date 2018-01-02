<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Helpers;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\I18n\TranslatorInterface;

class Requirements
{
    const REQUIRED_PHP_VERSION = '7.1.0';

    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Requirements constructor.
     * @param ApplicationPath $appPath
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ApplicationPath $appPath,
        TranslatorInterface $translator
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
        $requirements[0]['success'] = version_compare(phpversion(), self::REQUIRED_PHP_VERSION, '>=');
        $requirements[0]['found'] = phpversion();
        $requirements[0]['required'] = self::REQUIRED_PHP_VERSION;
        $requirements[1]['name'] = $this->translator->t('install', 'pdo_extension');
        $requirements[1]['success'] = extension_loaded('pdo') && extension_loaded('pdo_mysql');
        $requirements[1]['found'] = $this->translator->t(
            'install',
            $requirements[1]['success'] === true ? 'on' : 'off'
        );
        $requirements[1]['required'] = $this->translator->t('install', 'on');
        $requirements[2]['name'] = $this->translator->t('install', 'gd_library');
        $requirements[2]['success'] = extension_loaded('gd');
        $requirements[2]['found'] = $this->translator->t(
            'install',
            $requirements[2]['success'] === true ? 'on' : 'off'
        );
        $requirements[2]['required'] = $this->translator->t('install', 'on');

        $stopInstall = false;
        foreach ($requirements as $requirement) {
            if ($requirement['success'] === false) {
                $stopInstall = true;
                break;
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

            if ($requiredFilesAndDirs[$i]['exists'] === false || $requiredFilesAndDirs[$i]['writable'] === false) {
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
                'success' => ini_get('post_max_size') > 0,
                'value' => ini_get('post_max_size'),
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
            $result['exists'] = true;
            $result['exists_lang'] = $this->translator->t('install', 'found');
        } elseif (is_dir(ACP3_ROOT_DIR . $fileOrDirectory) === true) {
            $result['exists'] = true;
            $result['exists_lang'] = $this->translator->t('install', 'found');
        } else {
            $result['exists'] = false;
            $result['exists_lang'] = $this->translator->t('install', 'not_found');
        }
        $result['writable'] = is_writable(ACP3_ROOT_DIR . $fileOrDirectory) === true;
        $result['writable_lang'] = $result['writable'] === true ? $this->translator->t(
            'install',
            'writable'
        ) : $this->translator->t('install', 'not_writable');

        return $result;
    }

    /**
     * @return array
     */
    private function fetchRequiredFilesAndDirectories()
    {
        return ['ACP3/config.yml', 'var/', 'uploads/'];
    }
}
