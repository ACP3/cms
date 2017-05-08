<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Test\Helper;

use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\UpdateCheck;

class UpdateCheckTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateCheck
     */
    private $updateCheck;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dateMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $settingsMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $updateFileParserMock;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->updateCheck = new UpdateCheck(
            $this->dateMock,
            $this->settingsMock,
            $this->updateFileParserMock
        );
    }

    private function setUpMockObjects()
    {
        $this->dateMock = $this->getMockBuilder(Date::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->settingsMock = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->updateFileParserMock = $this->getMockBuilder(UpdateCheck\UpdateFileParser::class)
            ->setMethods(['parseUpdateFile'])
            ->getMock();
    }

    public function testDoNotRequestUpdateURI()
    {
        $this->dateMock->expects($this->once())
            ->method('timestamp')
            ->willReturn((new \DateTime())->modify('-1 hour')->format('U'));

        $this->settingsMock->expects($this->once())
            ->method('getSettings')
            ->willReturn([
                'update_last_check' => (new \DateTime())->modify('-1 hour')->format('U'),
                'update_new_version' => BootstrapInterface::VERSION,
                'update_new_version_url' => 'https://foo.bar/'
            ]);

        $this->updateFileParserMock->expects($this->never())
            ->method('parseUpdateFile');

        $update = [
            'installed_version' => BootstrapInterface::VERSION,
            'latest_version' => BootstrapInterface::VERSION,
            'is_latest' => true,
            'url' => 'https://foo.bar/'
        ];

        $this->assertEquals($update, $this->updateCheck->checkForNewVersion());
    }

    public function testDoRequestUpdateURI()
    {
        $this->dateMock->expects($this->exactly(2))
            ->method('timestamp')
            ->willReturn((new \DateTime())->format('U'));

        $this->settingsMock->expects($this->once())
            ->method('getSettings')
            ->willReturn([
                'update_last_check' => (new \DateTime())->modify('-2 days')->format('U'),
                'update_new_version' => BootstrapInterface::VERSION,
                'update_new_version_url' => 'https://foo.bar/'
            ]);

        $this->settingsMock->expects($this->once())
            ->method('saveSettings')
            ->willReturn(true);

        $this->updateFileParserMock->expects($this->once())
            ->method('parseUpdateFile')
            ->willReturn([
                'latest_version' => BootstrapInterface::VERSION,
                'url' => 'https://foobar.baz/'
            ]);

        $update = [
            'installed_version' => BootstrapInterface::VERSION,
            'latest_version' => BootstrapInterface::VERSION,
            'is_latest' => true,
            'url' => 'https://foobar.baz/'
        ];

        $this->assertEquals($update, $this->updateCheck->checkForNewVersion());
    }
}
