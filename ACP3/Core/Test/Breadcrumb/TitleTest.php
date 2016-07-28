<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Breadcrumb;


use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use Symfony\Component\EventDispatcher\EventDispatcher;

class TitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    protected $title;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stepsMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcherMock;

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->title = new Title(
            $this->stepsMock,
            $this->eventDispatcherMock
        );
    }

    protected function initializeMockObjects()
    {
        $this->stepsMock = $this->getMockBuilder(Steps::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventDispatcherMock = $this->getMockBuilder(EventDispatcher::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetSiteAndPageTitleWithNoCustomSiteTitle()
    {
        $this->setUpStepsExpectations(1);

        $this->assertEquals('Foo', $this->title->getSiteAndPageTitle());
    }

    protected function setUpStepsExpectations($callCount)
    {
        $steps = [
            [
                'title' => 'Foo',
                'uri' => '/foo/bar/baz/'
            ]
        ];

        $this->stepsMock->expects($this->exactly($callCount))
            ->method('getBreadcrumb')
            ->willReturn($steps);
    }

    public function testGetSiteAndPageTitleWithCustomSiteTitle()
    {
        $this->setUpStepsExpectations(1);
        
        $this->title->setSiteTitle('Lorem Ipsum');

        $this->assertEquals('Foo | Lorem Ipsum', $this->title->getSiteAndPageTitle());
    }

    public function testGetSiteAndPageTitleWithPrefixAndPostfixAndSeparator()
    {
        $this->setUpStepsExpectations(1);

        $this->title
            ->setSiteTitle('Lorem Ipsum')
            ->setPageTitlePrefix('ACP')
            ->setPageTitlePostfix('Page 1')
            ->setPageTitleSeparator('::');

        $expected = 'ACP :: Foo :: Page 1 | Lorem Ipsum';
        $this->assertEquals($expected, $this->title->getSiteAndPageTitle());
    }

    public function testGetSiteAndPageTitleWithCustomPageTitle()
    {
        $this->setUpStepsExpectations(0);

        $this->title
            ->setSiteTitle('Lorem Ipsum')
            ->setPageTitle('FooBar');

        $expected = 'FooBar | Lorem Ipsum';
        $this->assertEquals($expected, $this->title->getSiteAndPageTitle());
    }
}
