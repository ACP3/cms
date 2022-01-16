<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use Composer\Semver\VersionParser;

class UpdateCheckTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var UpdateCheck
     */
    private $updateCheck;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Date
     */
    private $dateMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|SettingsInterface
     */
    private $settingsMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\ACP3\Modules\ACP3\System\Helper\UpdateCheck\UpdateFileParser
     */
    private $updateFileParserMock;
    /**
     * @var \Composer\Semver\VersionParser
     */
    private $versionParser;

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

    public function testDoNotRequestUpdateURI(): void
    {
        $this->dateMock->expects(self::once())
            ->method('timestamp')
            ->willReturn((int) (new \DateTime())->modify('-1 hour')->format('U'));

        $this->settingsMock->expects(self::once())
            ->method('getSettings')
            ->willReturn([
                'update_last_check' => (new \DateTime())->modify('-1 hour')->format('U'),
                'update_new_version' => BootstrapInterface::VERSION,
                'update_new_version_url' => 'https://foo.bar/',
            ]);

        $this->updateFileParserMock->expects(self::never())
            ->method('parseUpdateFile');

        $update = [
            'installed_version' => $this->versionParser->normalize(BootstrapInterface::VERSION),
            'latest_version' => $this->versionParser->normalize(BootstrapInterface::VERSION),
            'is_latest' => true,
            'url' => 'https://foo.bar/',
        ];

        self::assertEquals($update, $this->updateCheck->checkForNewVersion());
    }

    public function testDoRequestUpdateURI(): void
    {
        $this->dateMock->expects(self::exactly(2))
            ->method('timestamp')
            ->willReturn((int) (new \DateTime())->format('U'));

        $this->settingsMock->expects(self::once())
            ->method('getSettings')
            ->willReturn([
                'update_last_check' => (new \DateTime())->modify('-2 days')->format('U'),
                'update_new_version' => BootstrapInterface::VERSION,
                'update_new_version_url' => 'https://foo.bar/',
            ]);

        $this->settingsMock->expects(self::once())
            ->method('saveSettings')
            ->willReturn(true);

        $this->updateFileParserMock->expects(self::once())
            ->method('parseUpdateFile')
            ->willReturn([
                'latest_version' => BootstrapInterface::VERSION,
                'url' => 'https://foobar.baz/',
            ]);

        $update = [
            'installed_version' => $this->versionParser->normalize(BootstrapInterface::VERSION),
            'latest_version' => $this->versionParser->normalize(BootstrapInterface::VERSION),
            'is_latest' => true,
            'url' => 'https://foobar.baz/',
        ];

        self::assertEquals($update, $this->updateCheck->checkForNewVersion());
    }
}
