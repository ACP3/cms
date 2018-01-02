<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\UpdateCheck\UpdateFileParser;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Composer\Semver\Comparator;

class UpdateCheck
{
    const UPDATE_CHECK_FILE = 'https://acp3.github.io/update.txt';
    const UPDATE_CHECK_DATE_OFFSET = 86400;

    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var Date
     */
    private $date;
    /**
     * @var UpdateFileParser
     */
    private $updateFileParser;

    /**
     * UpdateCheck constructor.
     * @param Date $date
     * @param SettingsInterface $settings
     * @param UpdateFileParser $updateFileParser
     */
    public function __construct(Date $date, SettingsInterface $settings, UpdateFileParser $updateFileParser)
    {
        $this->settings = $settings;
        $this->date = $date;
        $this->updateFileParser = $updateFileParser;
    }

    /**
     * @return array
     */
    public function checkForNewVersion()
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ($this->canRequestUpdateURI($settings['update_last_check'])) {
            $update = $this->doUpdateCheck();
        } else {
            $update = [
                'installed_version' => BootstrapInterface::VERSION,
                'latest_version' => $settings['update_new_version'],
                'is_latest' => $this->isLatestVersion($settings['update_new_version']),
                'url' => $settings['update_new_version_url']
            ];
        }

        return $update;
    }

    /**
     * @param string $lastUpdateTimestamp
     * @return bool
     */
    private function canRequestUpdateURI($lastUpdateTimestamp)
    {
        return $this->date->timestamp() - $lastUpdateTimestamp >= static::UPDATE_CHECK_DATE_OFFSET;
    }

    /**
     * @return array
     */
    private function doUpdateCheck()
    {
        try {
            $data = $this->updateFileParser->parseUpdateFile(static::UPDATE_CHECK_FILE);

            $update = [
                'installed_version' => BootstrapInterface::VERSION,
                'latest_version' => $data['latest_version'],
                'is_latest' => $this->isLatestVersion($data['latest_version']),
                'url' => $data['url']
            ];

            $this->saveUpdateSettings($update);
        } catch (\RuntimeException $e) {
            $update = [];
        }

        return $update;
    }

    /**
     * @param string $latestVersion
     * @return bool
     */
    private function isLatestVersion($latestVersion)
    {
        return Comparator::greaterThanOrEqualTo(
            BootstrapInterface::VERSION,
            $latestVersion
        );
    }

    /**
     * @param array $update
     * @return bool
     */
    private function saveUpdateSettings(array $update)
    {
        $data = [
            'update_last_check' => $this->date->timestamp(),
            'update_new_version' => $update['latest_version'],
            'update_new_version_url' => $update['url'],
        ];

        return $this->settings->saveSettings($data, Schema::MODULE_NAME);
    }
}
