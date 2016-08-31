<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Assets;


use ACP3\Core\Assets\Cache;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules\Vendor;
use ACP3\Core\XML;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileResolver
     */
    private $fileResolver;
    /**
     * @var XML
     */
    private $xml;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $assetsCache;
    /**
     * @var ApplicationPath
     */
    private $appPath;
    /**
     * @var Vendor
     */
    private $vendors;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->xml = new XML();
        $this->appPath = new ApplicationPath(ApplicationMode::DEVELOPMENT);
        $this->appPath->setDesignPathInternal(ACP3_ROOT_DIR . 'tests/designs/acp3/');
        $this->vendors = new Vendor($this->appPath);

        $this->fileResolver = new FileResolver(
            $this->xml,
            $this->assetsCache,
            $this->appPath,
            $this->vendors
        );
    }

    private function setUpMockObjects()
    {
        $this->assetsCache = $this->getMockBuilder(Cache::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testResolveTemplatePath()
    {
        $expected = $this->appPath->getModulesDir() . 'ACP3/System/Resources/View/Partials/breadcrumb.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/breadcrumb.tpl');
        $this->assertEquals($expected, $actual);
    }

    public function testResolveTemplatePathWithInheritance()
    {
        $expected = $this->appPath->getDesignPathInternal() . 'System/View/Partials/mark.tpl';
        $actual = $this->fileResolver->resolveTemplatePath('System/Partials/mark.tpl');
        $this->assertEquals($expected, $actual);
    }
}
