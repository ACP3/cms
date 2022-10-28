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
use Composer\Semver\VersionParser;
use PHPUnit\Framework\MockObject\MockObject;

class UpdateCheckTest extends \PHPUnit\Framework\TestCase
{
    private UpdateCheck $updateCheck;

    private Date|MockObject $dateMock;

    private SettingsInterface|MockObject $settingsMock;

    private UpdateFileParser|MockObject $updateFileParserMock;

    private VersionParser $versionParser;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $this->versionParser = new VersionParser();

        $this->updateCheck = new UpdateCheck(
            $this->dateMock,
            $this->settingsMock,
            $this->updateFileParserMock,
            $this->versionParser
        );
    }

    private function setUpMockObjects(): void
    {
        $this->dateMock = $this->createMock(Date::class);
        $this->settingsMock = $this->createMock(SettingsInterface::class);
        $this->updateFileParserMock = $this->createMock(UpdateCheck\UpdateFileParser::class);
    }

    private function setUpSettingsMockExpectation(string $lastCheck = '-1 hour'): void
    {
        $this->settingsMock->expects(self::once())
            ->method('getSettings')
            ->willReturn([
                'update_last_check' => (new \DateTime())->modify($lastCheck)->format('U'),
                'update_new_version' => BootstrapInterface::VERSION,
                'update_new_version_url' => 'https://foo.bar/',
            ]);
    }

    public function testDoNotRequestUpdateURI(): void
    {
        $this->dateMock->expects(self::once())
            ->method('timestamp')
            ->willReturn((int) (new \DateTime())->modify('-1 hour')->format('U'));
        $this->setUpSettingsMockExpectation();

        $this->updateFileParserMock->expects(self::never())
            ->method('parseUpdateFile');

        $this->updateCheck->checkForNewVersion();
    }

    public function testDoRequestUpdateURI(): void
    {
        $this->dateMock->expects(self::exactly(2))
            ->method('timestamp')
            ->willReturn((int) (new \DateTime())->format('U'));

        $this->setUpSettingsMockExpectation('-2 days');

        $this->settingsMock->expects(self::once())
            ->method('saveSettings')
            ->willReturn(true);

        $this->updateFileParserMock->expects(self::once())
            ->method('parseUpdateFile')
            ->willReturn([
                'latest_version' => BootstrapInterface::VERSION,
                'url' => 'https://foobar.baz/',
            ]);

        $this->updateCheck->checkForNewVersion();
    }

    public function testGetLatestUpdateCheckInformation(): void
    {
        $this->setUpSettingsMockExpectation();

        $update = [
            'installed_version' => $this->versionParser->normalize(BootstrapInterface::VERSION),
            'latest_version' => $this->versionParser->normalize(BootstrapInterface::VERSION),
            'is_latest' => true,
            'url' => 'https://foo.bar/',
        ];

        self::assertEquals($update, $this->updateCheck->getLatestUpdateCheckInformation());
    }
}
