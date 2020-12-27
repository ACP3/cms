<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Helpers;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;

class Requirements
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \Composer\Semver\VersionParser
     */
    private $versionParser;

    public function __construct(Modules $modules, Translator $translator, VersionParser $versionParser)
    {
        $this->translator = $translator;
        $this->modules = $modules;
        $this->versionParser = $versionParser;
    }

    /**
     * Checks, whether the mandatory system requirements of the ACP3 are fulfilled.
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function checkMandatoryRequirements(): array
    {
        $minimumPHPVersion = $this->getRequiredPHPVersion();

        $requirements = [];
        $requirements[0]['name'] = $this->translator->t('installer', 'php_version');
        $requirements[0]['satisfied'] = Comparator::greaterThanOrEqualTo(PHP_VERSION, $minimumPHPVersion);
        $requirements[0]['found'] = PHP_VERSION;
        $requirements[0]['required'] = $minimumPHPVersion;
        $requirements[1]['name'] = $this->translator->t('installer', 'pdo_extension');
        $requirements[1]['satisfied'] = \extension_loaded('pdo') && \extension_loaded('pdo_mysql');
        $requirements[1]['found'] = $this->translator->t(
            'installer',
            $requirements[1]['satisfied'] ? 'on' : 'off'
        );
        $requirements[1]['required'] = $this->translator->t('installer', 'on');
        $requirements[2]['name'] = $this->translator->t('installer', 'gd_library');
        $requirements[2]['satisfied'] = \extension_loaded('gd');
        $requirements[2]['found'] = $this->translator->t(
            'installer',
            $requirements[2]['satisfied'] ? 'on' : 'off'
        );
        $requirements[2]['required'] = $this->translator->t('installer', 'on');

        $stopInstall = false;
        foreach ($requirements as $requirement) {
            if (!$requirement['satisfied']) {
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
     */
    public function checkFolderAndFilePermissions(): array
    {
        $defaults = $this->fetchRequiredFilesAndDirectories();
        $requiredFilesAndDirs = [];
        $checkAgain = false;

        $i = 0;
        foreach ($defaults as $fileOrDirectory) {
            $requiredFilesAndDirs[$i] = $this->requiredFileOrFolderHasPermission($fileOrDirectory);

            if (!$requiredFilesAndDirs[$i]['exists'] || !$requiredFilesAndDirs[$i]['writable']) {
                $checkAgain = true;
            }
            ++$i;
        }

        return [$requiredFilesAndDirs, $checkAgain];
    }

    public function checkOptionalRequirements(): array
    {
        return [
            [
                'setting' => $this->translator->t('installer', 'maximum_uploadsize'),
                'satisfied' => \ini_get('post_max_size') > 0,
                'value' => \ini_get('post_max_size'),
            ],
        ];
    }

    private function requiredFileOrFolderHasPermission(string $fileOrDirectory): array
    {
        $result = [];
        $result['path'] = $fileOrDirectory;
        // Überprüfen, ob es eine Datei oder ein Ordner ist
        if (\is_file(ACP3_ROOT_DIR . DIRECTORY_SEPARATOR . $fileOrDirectory) === true) {
            $result['exists'] = true;
        } elseif (\is_dir(ACP3_ROOT_DIR . DIRECTORY_SEPARATOR . $fileOrDirectory) === true) {
            $result['exists'] = true;
        } else {
            $result['exists'] = false;
        }
        $result['writable'] = \is_writable(ACP3_ROOT_DIR . DIRECTORY_SEPARATOR . $fileOrDirectory) === true;

        return $result;
    }

    private function fetchRequiredFilesAndDirectories(): array
    {
        return ['/ACP3/config.yml', '/cache/', '/uploads/', '/uploads/assets/'];
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function getRequiredPHPVersion(): ?string
    {
        $modules = $this->modules->getAllModulesTopSorted();

        $minimumPHPVersion = null;

        foreach ($modules as $module) {
            $composerJsonPath = $module['dir'] . '/composer.json';

            if (!\is_file($composerJsonPath)) {
                continue;
            }

            $composerJsoData = \json_decode(\file_get_contents($composerJsonPath), true);

            if (!isset($composerJsoData['require']) || !\array_key_exists('php', $composerJsoData['require'])) {
                continue;
            }

            $constraint = $this->versionParser->parseConstraints($composerJsoData['require']['php']);
            $normalizedVersion = $constraint->getLowerBound()->getVersion();

            if ($minimumPHPVersion === null) {
                $minimumPHPVersion = $normalizedVersion;

                continue;
            }

            if (Comparator::greaterThanOrEqualTo($normalizedVersion, $minimumPHPVersion)) {
                $minimumPHPVersion = $normalizedVersion;
            }
        }

        return $minimumPHPVersion;
    }
}
