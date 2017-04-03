<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Test\Core\Breadcrumb;

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

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->title = new Title(
            $this->stepsMock,
            $this->eventDispatcherMock,
            $this->configMock
        );
    }

    protected function initializeMockObjects()
    {
        parent::initializeMockObjects();

        $this->configMock = $this->getMockBuilder(SettingsInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();
    }

    public function testGetSiteAndPageTitleWithNoCustomSiteTitle()
    {
        $this->setUpStepsExpectations(1);

        $this->configMock->expects($this->exactly(2))
            ->method('getSettings')
            ->with('system')
            ->willReturn([
                'site_title' => 'SEO Title',
            ]);

        $this->assertEquals('Foo | SEO Title', $this->title->getSiteAndPageTitle());
    }

    public function testGetSiteAndPageTitleWithSubtitle()
    {
        $this->setUpStepsExpectations(1);

        $this->configMock->expects($this->exactly(2))
            ->method('getSettings')
            ->with('system')
            ->willReturn([
                'site_title' => 'SEO Title',
                'site_subtitle' => 'Subtitle'
            ]);

        $this->assertEquals('Foo | SEO Title - Subtitle', $this->title->getSiteAndPageTitle());
    }
}
