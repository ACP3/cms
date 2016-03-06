<?php
namespace ACP3\Core\Test;


use ACP3\Core\Assets;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;

class AssetsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Assets
     */
    private $assets;

    protected function setUp()
    {
        $appPath = new ApplicationPath(ApplicationMode::DEVELOPMENT);
        $appPath->setDesignPathInternal(ACP3_ROOT_DIR . 'tests/designs/acp3/');

        $this->assets = new Assets($appPath);
    }

    public function testDefaultLibrariesEnabled()
    {
        $libraries = $this->assets->getEnabledLibrariesAsString();
        $this->assertEquals('jquery,bootstrap', $libraries);
    }

    public function testEnableDatepicker()
    {
        $this->assets->enableLibraries(['datetimepicker']);

        $libraries = $this->assets->getEnabledLibrariesAsString();
        $this->assertEquals('moment,jquery,bootstrap,datetimepicker', $libraries);
    }

    public function testFetchAdditionalThemeCssFiles()
    {
        $files = $this->assets->fetchAdditionalThemeCssFiles();

        $this->assertEquals(['additional-style.css'], $files);
    }

    public function testFetchAdditionalThemeJsFiles()
    {
        $files = $this->assets->fetchAdditionalThemeJsFiles();

        $this->assertEquals(['additional-script.js'], $files);
    }

}
