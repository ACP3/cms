<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\UpdateCheck\UpdateFileParser;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;

class UpdateCheck
{
    private const UPDATE_CHECK_FILE = 'https://acp3.gitlab.io/update-check/update.txt';
    private const UPDATE_CHECK_DATE_OFFSET = 86400;

    public function __construct(private Date $date, private SettingsInterface $settings, private UpdateFileParser $updateFileParser, private VersionParser $versionParser)
    {
    }

    /**
     * @return array<string, string|bool>
     */
    public function checkForNewVersion(): array
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ($this->canRequestUpdateURI($settings['update_last_check'])) {
            $update = $this->doUpdateCheck();
        } else {
            $update = [
                'installed_version' => $this->versionParser->normalize(BootstrapInterface::VERSION),
                'latest_version' => $this->versionParser->normalize($settings['update_new_version']),
                'is_latest' => $this->isLatestVersion($settings['update_new_version']),
                'url' => $settings['update_new_version_url'],
            ];
        }

        return $update;
    }

    private function canRequestUpdateURI(int $lastUpdateTimestamp): bool
    {
        return $this->date->timestamp() - $lastUpdateTimestamp >= self::UPDATE_CHECK_DATE_OFFSET;
    }

    /**
     * @return array<string, string|bool>
     */
    private function doUpdateCheck(): array
    {
        try {
            $data = $this->updateFileParser->parseUpdateFile(self::UPDATE_CHECK_FILE);

            $update = [
                'installed_version' => $this->versionParser->normalize(BootstrapInterface::VERSION),
                'latest_version' => $this->versionParser->normalize($data['latest_version']),
                'is_latest' => $this->isLatestVersion($data['latest_version']),
                'url' => $data['url'],
            ];

            $this->saveUpdateSettings($update);
        } catch (\RuntimeException) {
            $update = [];
        }

        return $update;
    }

    private function isLatestVersion(string $latestVersion): bool
    {
        return Comparator::greaterThanOrEqualTo(
            $this->versionParser->normalize(BootstrapInterface::VERSION),
            $this->versionParser->normalize($latestVersion)
        );
    }

    /**
     * @param array<string, string|bool> $update
     *
     * @throws \Exception
     */
    private function saveUpdateSettings(array $update): bool
    {
        $data = [
            'update_last_check' => $this->date->timestamp(),
            'update_new_version' => $update['latest_version'],
            'update_new_version_url' => $update['url'],
        ];

        return $this->settings->saveSettings($data, Schema::MODULE_NAME);
    }
}
