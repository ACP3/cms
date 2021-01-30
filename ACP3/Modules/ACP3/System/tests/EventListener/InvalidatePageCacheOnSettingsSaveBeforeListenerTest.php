<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\EventListener;

use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Model\Repository\SettingsRepository;
use ACP3\Modules\ACP3\System\Services\CacheClearService;

class InvalidatePageCacheOnSettingsSaveBeforeListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var InvalidatePageCacheOnSettingsSaveBeforeListener
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
     * @var \PHPUnit\Framework\MockObject\MockObject & Modules
     */
    private $modulesMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & SettingsRepository
     */
    private $settingsRepositoryMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject & CacheClearService
     */
    private $cacheClearServiceMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $this->invalidatePageCache = new InvalidatePageCacheOnSettingsSaveBeforeListener(
            $this->settingsMock,
            $this->modulesMock,
            $this->settingsRepositoryMock,
            $this->canUsePageCacheMock,
            $this->cacheClearServiceMock
        );
    }

    private function setUpMockObjects(): void
    {
        $this->settingsMock = $this->createMock(SettingsInterface::class);
        $this->modulesMock = $this->createMock(Modules::class);
        $this->settingsRepositoryMock = $this->createMock(SettingsRepository::class);
        $this->canUsePageCacheMock = $this->createMock(CanUsePageCache::class);
        $this->cacheClearServiceMock = $this->createMock(CacheClearService::class);
    }

    public function testDisabledPageCache(): void
    {
        $this->setUpCanUsePageCacheMockExpectations(false);
        $this->setUpSettingsMockExpectations();

        ($this->invalidatePageCache)();
    }

    private function setUpCanUsePageCacheMockExpectations($cacheEnabled = true): void
    {
        $this->canUsePageCacheMock->expects(self::once())
            ->method('canUsePageCache')
            ->willReturn($cacheEnabled);
    }

    private function setUpSettingsMockExpectations($methodCalls = 0, $purgeMode = 1): void
    {
        $this->settingsMock->expects(self::exactly($methodCalls))
            ->method('getSettings')
            ->with('system')
            ->willReturn(['page_cache_purge_mode' => $purgeMode]);
    }

    public function testManualPageCachePurge(): void
    {
        $this->setUpCanUsePageCacheMockExpectations();
        $this->setUpSettingsMockExpectations(1, 2);

        ($this->invalidatePageCache)();
    }

    public function testAutomaticPageCachePurge(): void
    {
        $this->setUpCanUsePageCacheMockExpectations();
        $this->setUpSettingsMockExpectations(1, 2);

        $this->modulesMock->expects(self::once())
            ->method('getModuleId')
            ->with('system')
            ->willReturn(1);

        $this->settingsRepositoryMock->expects(self::once())
            ->method('update')
            ->with(['value' => false], ['module_id' => 1, 'name' => 'page_cache_is_valid']);

        ($this->invalidatePageCache)();
    }
}
