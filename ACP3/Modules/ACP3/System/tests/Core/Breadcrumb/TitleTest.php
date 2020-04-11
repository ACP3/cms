<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\Breadcrumb;

use ACP3\Core\Http\Request;
use ACP3\Core\Settings\SettingsInterface;

class TitleTest extends \ACP3\Core\Breadcrumb\TitleTest
{
    /**
     * @var Title
     */
    protected $title;
    /**
     * @var TitleConfigurator
     */
    private $titleConfigurator;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $configMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;

    protected function setup(): void
    {
        $this->initializeMockObjects();
        $this->setUpTitleConfigurator();

        $this->title = new Title(
            $this->requestMock,
            $this->stepsMock,
            $this->eventDispatcherMock,
            $this->configMock
        );
    }

    protected function initializeMockObjects()
    {
        parent::initializeMockObjects();

        $this->requestMock = $this->createMock(Request::class);
        $this->configMock = $this->createMock(SettingsInterface::class);
    }

    private function setUpTitleConfigurator()
    {
        $this->titleConfigurator = new TitleConfigurator($this->configMock);
    }

    public function testGetSiteAndPageTitleWithCustomSiteTitle()
    {
        $this->setUpConfigMockExpectations('SEO Title', '', 1, 0);
        $this->titleConfigurator->configure($this->title);

        parent::testGetSiteAndPageTitleWithCustomSiteTitle();
    }

    public function testGetSiteAndPageTitleWithNoCustomSiteTitle()
    {
        $this->setUpStepsExpectations(1);

        $this->setUpConfigMockExpectations('SEO Title', '', 1, 0);
        $this->titleConfigurator->configure($this->title);

        $this->assertEquals('Foo | SEO Title', $this->title->getSiteAndPageTitle());
    }

    private function setUpConfigMockExpectations(
        string $siteTitle,
        string $siteSubtitle,
        int $subtitleMode,
        int $subtitleHomepageMode)
    {
        $this->configMock->expects($this->atLeastOnce())
            ->method('getSettings')
            ->with('system')
            ->willReturn([
                'site_title' => $siteTitle,
                'site_subtitle' => $siteSubtitle,
                'site_subtitle_homepage_mode' => $subtitleHomepageMode,
                'site_subtitle_mode' => $subtitleMode,
            ]);
    }

    public function testGetSiteAndPageTitleWithSubtitle()
    {
        $this->setUpStepsExpectations(1);

        $this->setUpConfigMockExpectations('SEO Title', 'Subtitle', 1, 0);
        $this->titleConfigurator->configure($this->title);

        $this->assertEquals('Foo | SEO Title - Subtitle', $this->title->getSiteAndPageTitle());
    }

    public function testGetSiteAndPageTitleWithPrefixAndPostfixAndSeparator()
    {
        $this->setUpConfigMockExpectations('SEO Title', 'Subtitle', 1, 0);

        parent::testGetSiteAndPageTitleWithPrefixAndPostfixAndSeparator();
    }

    public function testGetSiteAndPageTitleForHomepageWithOverride()
    {
        $this->setUpStepsExpectations(0);

        $this->requestMock->expects($this->atLeastOnce())
            ->method('isHomepage')
            ->willReturn(true);

        $this->setUpConfigMockExpectations('SEO Title', 'Subtitle', 1, 1);
        $this->titleConfigurator->configure($this->title);

        $this->assertEquals('SEO Title - Subtitle', $this->title->getSiteAndPageTitle());
    }

    public function testGetSiteAndPageTitleForNotHomepage()
    {
        $this->setUpStepsExpectations(1);

        $this->requestMock->expects($this->atLeastOnce())
            ->method('isHomepage')
            ->willReturn(false);

        $this->setUpConfigMockExpectations('SEO Title', 'Subtitle', 2, 1);
        $this->titleConfigurator->configure($this->title);

        $this->assertEquals('Foo | SEO Title', $this->title->getSiteAndPageTitle());
    }

    public function testGetSiteAndPageTitleWithCustomPageTitle()
    {
        $this->setUpConfigMockExpectations('SEO Title', 'Subtitle', 1, 0);

        parent::testGetSiteAndPageTitleWithCustomPageTitle();
    }

    public function testGetSiteAndPageTitleMetaTitleTakesPrecedenceOverPageTitle()
    {
        $this->setUpConfigMockExpectations('SEO Title', 'Subtitle', 1, 0);

        parent::testGetSiteAndPageTitleMetaTitleTakesPrecedenceOverPageTitle();
    }
}
