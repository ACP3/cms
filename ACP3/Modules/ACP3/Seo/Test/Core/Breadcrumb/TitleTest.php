<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Test\Core\Breadcrumb;


use ACP3\Core\Config;
use ACP3\Modules\ACP3\Seo\Core\Breadcrumb\Title;

class TitleTest extends \ACP3\Core\Test\Breadcrumb\TitleTest
{
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

        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configMock->expects($this->once())
            ->method('getSettings')
            ->with('seo')
            ->willReturn([
                'title' => 'SEO Title'
            ]);
    }

    public function testGetSiteAndPageTitleWithNoCustomSiteTitle()
    {
        $this->setUpStepsExpectations(1);

        $this->assertEquals('Foo | SEO Title', $this->title->getSiteAndPageTitle());
    }
}
