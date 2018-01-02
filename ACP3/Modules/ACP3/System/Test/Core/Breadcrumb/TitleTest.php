<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Test\Core\Breadcrumb;

use ACP3\Core\Http\Request;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Core\Breadcrumb\Title;

class TitleTest extends \ACP3\Core\Test\Breadcrumb\TitleTest
{
    /**
     * @var Title
     */
    protected $title;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    protected function setUp()
    {
        $this->initializeMockObjects();

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

        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configMock = $this->getMockBuilder(SettingsInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();
    }

    public function testGetSiteAndPageTitleWithNoCustomSiteTitle()
    {
        $this->setUpStepsExpectations(1);

        $this->setUpConfigMockExpectations('SEO Title', '', 1, 0);

        $this->assertEquals('Foo | SEO Title', $this->title->getSiteAndPageTitle());
    }

    /**
     * @param string $siteTitle
     * @param string $siteSubtitle
     * @param int $subtitleMode
     * @param int $subtitleHomepageMode
     */
    private function setUpConfigMockExpectations($siteTitle, $siteSubtitle, $subtitleMode, $subtitleHomepageMode)
    {
        $this->configMock->expects($this->once())
            ->method('getSettings')
            ->with('system')
            ->willReturn([
                'site_title' => $siteTitle,
                'site_subtitle' => $siteSubtitle,
                'site_subtitle_homepage_mode' => $subtitleHomepageMode,
                'site_subtitle_mode' => $subtitleMode
            ]);
    }

    public function testGetSiteAndPageTitleWithSubtitle()
    {
        $this->setUpStepsExpectations(1);

        $this->setUpConfigMockExpectations('SEO Title', 'Subtitle', 1, 0);

        $this->assertEquals('Foo | SEO Title - Subtitle', $this->title->getSiteAndPageTitle());
    }

    public function testGetSiteAndPageTitleForHomepageWithOverride()
    {
        $this->setUpStepsExpectations(0);

        $this->requestMock->expects($this->once())
            ->method('isHomepage')
            ->willReturn(true);

        $this->setUpConfigMockExpectations('SEO Title', 'Subtitle', 1, 1);

        $this->assertEquals('Subtitle | SEO Title', $this->title->getSiteAndPageTitle());
    }

    public function testGetSiteAndPageTitleForNotHomepage()
    {
        $this->setUpStepsExpectations(1);

        $this->requestMock->expects($this->once())
            ->method('isHomepage')
            ->willReturn(false);

        $this->setUpConfigMockExpectations('SEO Title', 'Subtitle', 2, 1);

        $this->assertEquals('Foo | SEO Title', $this->title->getSiteAndPageTitle());
    }

    public function testGetSiteAndPageTitleWithCustomPageTitle()
    {
        $this->setUpConfigMockExpectations('', '', 1, 0);

        parent::testGetSiteAndPageTitleWithCustomPageTitle();
    }

    public function testGetSiteAndPageTitleWithCustomSiteTitle()
    {
        $this->setUpConfigMockExpectations('', '', 1, 0);

        parent::testGetSiteAndPageTitleWithCustomSiteTitle();
    }

    public function testGetSiteAndPageTitleWithPrefixAndPostfixAndSeparator()
    {
        $this->setUpConfigMockExpectations('', '', 1, 0);

        parent::testGetSiteAndPageTitleWithPrefixAndPostfixAndSeparator();
    }
}
