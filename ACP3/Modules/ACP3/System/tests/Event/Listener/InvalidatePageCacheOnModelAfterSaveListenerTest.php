<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Services\CacheClearService;

class InvalidatePageCacheOnModelAfterSaveListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var InvalidatePageCacheOnModelAfterSaveListener
     */
    private $invalidatePageCache;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & SettingsInterface
     */
    private $settingsMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & CanUsePageCache
     */
    private $canUsePageCacheMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & CacheClearService
     */
    private $cacheClearServiceMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $this->invalidatePageCache = new InvalidatePageCacheOnModelAfterSaveListener(
            $this->settingsMock,
            $this->canUsePageCacheMock,
            $this->cacheClearServiceMock
        );
    }

    private function setUpMockObjects()
    {
        $this->settingsMock = $this->createMock(SettingsInterface::class);
        $this->canUsePageCacheMock = $this->createMock(CanUsePageCache::class);
        $this->cacheClearServiceMock = $this->createMock(CacheClearService::class);
    }

    public function testDisabledPageCache()
    {
        $this->setUpCanUsePageCacheMockExpectations(false);
        $this->setUpSettingsMockExpectations();

        $this->invalidatePageCache->__invoke();
    }

    private function setUpCanUsePageCacheMockExpectations($cacheEnabled = true)
    {
        $this->canUsePageCacheMock->expects(self::once())
            ->method('canUsePageCache')
            ->willReturn($cacheEnabled);
    }

    private function setUpSettingsMockExpectations($methodCalls = 0, $purgeMode = 1)
    {
        $this->settingsMock->expects(self::exactly($methodCalls))
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

        $this->settingsMock->expects(self::once())
            ->method('saveSettings')
            ->with(['page_cache_is_valid' => false], 'system');

        $this->invalidatePageCache->__invoke();
    }
}
