<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Test\Event\Listener;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Event\Listener\InvalidatePageCacheOnSettingsSaveBeforeListener;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Model\Repository\SettingsRepository;

class InvalidatePageCacheOnSettingsSaveBeforeListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InvalidatePageCacheOnSettingsSaveBeforeListener
     */
    private $invalidatePageCache;
    /**
     * @var ApplicationPath
     */
    private $applicationPath;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $settingsMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $canUsePageCacheMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $modulesMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $settingsRepositoryMock;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->invalidatePageCache = new InvalidatePageCacheOnSettingsSaveBeforeListener(
            $this->applicationPath,
            $this->settingsMock,
            $this->modulesMock,
            $this->settingsRepositoryMock,
            $this->canUsePageCacheMock
        );
    }

    private function setUpMockObjects()
    {
        $this->applicationPath = new ApplicationPath(ApplicationMode::DEVELOPMENT);
        $this->settingsMock = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();
        $this->modulesMock = $this->getMockBuilder(Modules::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->settingsRepositoryMock = $this->getMockBuilder(SettingsRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->canUsePageCacheMock = $this->getMockBuilder(CanUsePageCache::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testDisabledPageCache()
    {
        $this->setUpCanUsePageCacheMockExpectations(false);
        $this->setUpSettingsMockExpectations();

        $this->invalidatePageCache->invalidatePageCache();
    }

    private function setUpCanUsePageCacheMockExpectations($cacheEnabled = true)
    {
        $this->canUsePageCacheMock->expects($this->once())
            ->method('canUsePageCache')
            ->willReturn($cacheEnabled);
    }

    private function setUpSettingsMockExpectations($methodCalls = 0, $purgeMode = 1)
    {
        $this->settingsMock->expects($this->exactly($methodCalls))
            ->method('getSettings')
            ->with('system')
            ->willReturn(['page_cache_purge_mode' => $purgeMode]);
    }

    public function testManualPageCachePurge()
    {
        $this->setUpCanUsePageCacheMockExpectations(true);
        $this->setUpSettingsMockExpectations(1, 2);

        $this->invalidatePageCache->invalidatePageCache();
    }

    public function testAutomaticPageCachePurge()
    {
        $this->setUpCanUsePageCacheMockExpectations(true);
        $this->setUpSettingsMockExpectations(1, 2);

        $this->modulesMock->expects($this->once())
            ->method('getModuleId')
            ->with('system')
            ->willReturn(1);

        $this->settingsRepositoryMock->expects($this->once())
            ->method('update')
            ->with(['value' => false], ['module_id' => 1, 'name' => 'page_cache_is_valid']);

        $this->invalidatePageCache->invalidatePageCache();
    }
}
