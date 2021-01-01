<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Assets\LibrariesCache;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Model\Repository\SettingsRepository;
use Toflar\Psr6HttpCacheStore\Psr6Store;

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
     * @var \PHPUnit\Framework\MockObject\MockObject & Psr6Store
     */
    private $httpCacheStoreMock;
    /**
     * @var \ACP3\Core\Assets\LibrariesCache & \PHPUnit\Framework\MockObject\MockObject
     */
    private $librariesCacheMock;

    protected function setup(): void
    {
        $this->setUpMockObjects();

        $this->invalidatePageCache = new InvalidatePageCacheOnSettingsSaveBeforeListener(
            $this->settingsMock,
            $this->modulesMock,
            $this->settingsRepositoryMock,
            $this->canUsePageCacheMock,
            $this->httpCacheStoreMock,
            $this->librariesCacheMock
        );
    }

    private function setUpMockObjects()
    {
        $this->settingsMock = $this->createMock(SettingsInterface::class);
        $this->modulesMock = $this->createMock(Modules::class);
        $this->settingsRepositoryMock = $this->createMock(SettingsRepository::class);
        $this->canUsePageCacheMock = $this->createMock(CanUsePageCache::class);
        $this->httpCacheStoreMock = $this->createMock(Psr6Store::class);
        $this->librariesCacheMock = $this->createMock(LibrariesCache::class);
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

        $this->modulesMock->expects(self::once())
            ->method('getModuleId')
            ->with('system')
            ->willReturn(1);

        $this->settingsRepositoryMock->expects(self::once())
            ->method('update')
            ->with(['value' => false], ['module_id' => 1, 'name' => 'page_cache_is_valid']);

        $this->invalidatePageCache->__invoke();
    }
}
