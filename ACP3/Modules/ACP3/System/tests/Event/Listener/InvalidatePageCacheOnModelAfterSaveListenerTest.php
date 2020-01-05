<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;

class InvalidatePageCacheOnModelAfterSaveListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var InvalidatePageCacheOnModelAfterSaveListener
     */
    private $invalidatePageCache;
    /**
     * @var ApplicationPath
     */
    private $applicationPath;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $settingsMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $canUsePageCacheMock;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->invalidatePageCache = new InvalidatePageCacheOnModelAfterSaveListener(
            $this->applicationPath,
            $this->settingsMock,
            $this->canUsePageCacheMock
        );
    }

    private function setUpMockObjects()
    {
        $this->applicationPath = new ApplicationPath(ApplicationMode::DEVELOPMENT);
        $this->settingsMock = $this->createMock(SettingsInterface::class);
        $this->canUsePageCacheMock = $this->createMock(CanUsePageCache::class);
    }

    public function testDisabledPageCache()
    {
        $this->setUpCanUsePageCacheMockExpectations(false);
        $this->setUpSettingsMockExpectations();

        $this->invalidatePageCache->__invoke();
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

        $this->invalidatePageCache->__invoke();
    }

    public function testAutomaticPageCachePurge()
    {
        $this->setUpCanUsePageCacheMockExpectations(true);
        $this->setUpSettingsMockExpectations(1, 2);

        $this->settingsMock->expects($this->once())
            ->method('saveSettings')
            ->with(['page_cache_is_valid' => false], 'system');

        $this->invalidatePageCache->__invoke();
    }
}
