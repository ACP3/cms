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
    public function __construct(private Modules $modules, private Translator $translator, private VersionParser $versionParser)
    {
    }

    /**
     * Checks, whether the mandatory system requirements of the ACP3 are fulfilled.
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function checkMandatoryRequirements(): array
    {
        $modules = $this->modules->getAllModulesTopSorted();

        $minimumPHPVersion = $this->getRequiredPHPVersion($modules);
        $requiredPHPExtensions = $this->getRequiredPHPExtensions($modules);

        $requirements = [
            [
                'name' => $this->translator->t('installer', 'php_version'),
                'satisfied' => Comparator::greaterThanOrEqualTo(PHP_VERSION, $minimumPHPVersion),
                'found' => PHP_VERSION,
                'required' => $minimumPHPVersion,
            ],
        ];

        foreach ($requiredPHPExtensions as $requiredPHPExtension) {
            $extensionLoaded = \extension_loaded(substr($requiredPHPExtension, 4));

            $requirements[] = [
                'name' => $requiredPHPExtension,
                'satisfied' => $extensionLoaded,
                'found' => $this->translator->t(
                    'installer',
                    $extensionLoaded ? 'on' : 'off'
                ),
                'required' => $this->translator->t('installer', 'on'),
            ];
        }

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

        foreach ($defaults as $fileOrDirectory => $type) {
            $requiredFileOrDir = $this->requiredFileOrFolderHasPermission($fileOrDirectory, $type);

            if (!$requiredFileOrDir['exists'] || !$requiredFileOrDir['writable']) {
                $checkAgain = true;
            }

            $requiredFilesAndDirs[] = $requiredFileOrDir;
        }

        return [$requiredFilesAndDirs, $checkAgain];
    }

    public function checkOptionalRequirements(): array
    {
        return [
            [
                'setting' => $this->translator->t('installer', 'maximum_uploadsize'),
                'satisfied' => ini_get('post_max_size') > 0,
                'value' => ini_get('post_max_size'),
            ],
        ];
    }

    private function requiredFileOrFolderHasPermission(string $fileOrDirectory, string $type): array
    {
        $path = ACP3_ROOT_DIR . DIRECTORY_SEPARATOR . $fileOrDirectory;

        $result = [];
        $result['path'] = $fileOrDirectory;
        $result['writable'] = is_writable($path) === true;

        $result['exists'] = match ($type) {
            'file' => is_file($path) === true,
            'directory' => is_dir($path) === true,
            default => false,
        };

        return $result;
    }

    private function fetchRequiredFilesAndDirectories(): array
    {
        return [
            '/ACP3/config.yml' => 'file',
            '/cache/' => 'directory',
            '/uploads/' => 'directory',
            '/uploads/assets/' => 'directory',
        ];
    }

    private function getRequiredPHPVersion(array $modules): ?string
    {
        $minimumPHPVersion = null;

        foreach ($modules as $module) {
            $composerJsonPath = $module['dir'] . '/composer.json';

            if (!is_file($composerJsonPath)) {
                continue;
            }

            $composerJsoData = json_decode(file_get_contents($composerJsonPath), true, 512, JSON_THROW_ON_ERROR);

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

    private function getRequiredPHPExtensions(array $modules): array
    {
        $extensions = [];

        foreach ($modules as $module) {
            $composerJsonPath = $module['dir'] . '/composer.json';

            if (!is_file($composerJsonPath)) {
                continue;
            }

            $composerJsoData = json_decode(file_get_contents($composerJsonPath), true, 512, JSON_THROW_ON_ERROR);

            if (!isset($composerJsoData['require'])) {
                continue;
            }

            $componentExtensions = array_filter(array_keys($composerJsoData['require']), static fn ($packages) => str_starts_with($packages, 'ext-'));

            $extensions = [...$extensions, ...$componentExtensions];
        }

        sort($extensions);

        return $extensions;
    }
}
