<?php
namespace ACP3\Core;

use ACP3\Core\Environment\ApplicationPath;

/**
 * Class Assets
 * @package ACP3\Core
 */
class Assets
{
    /**
     * Legt fest, welche Bibliotheken beim Seitenaufruf geladen werden sollen
     * @var array
     */
    protected $libraries = [
        'moment' => [
            'enabled' => false,
            'js' => 'moment.min.js'
        ],
        'jquery' => [
            'enabled' => true,
            'js' => 'jquery.min.js'
        ],
        'fancybox' => [
            'enabled' => false,
            'dependencies' => ['jquery'],
            'css' => 'jquery.fancybox.css',
            'js' => 'jquery.fancybox.min.js'
        ],
        'bootstrap' => [
            'enabled' => false,
            'dependencies' => ['jquery'],
            'css' => 'bootstrap.min.css',
            'js' => 'bootstrap.min.js'
        ],
        'datatables' => [
            'enabled' => false,
            'dependencies' => ['bootstrap'],
            'css' => 'dataTables.bootstrap.css',
            'js' => 'jquery.datatables.min.js'
        ],
        'bootbox' => [
            'enabled' => false,
            'dependencies' => ['bootstrap'],
            'js' => 'bootbox.js'
        ],
        'datetimepicker' => [
            'enabled' => false,
            'dependencies' => ['jquery', 'moment'],
            'css' => 'bootstrap-datetimepicker.css',
            'js' => 'bootstrap-datetimepicker.min.js'
        ],
    ];

    /**
     * @var array
     */
    protected $additionalThemeCssFiles = [];
    /**
     * @var array
     */
    protected $additionalThemeJsFiles = [];
    /**
     * @var string
     */
    protected $enabledLibraries = '';
    /**
     * @var \SimpleXMLElement
     */
    protected $designXml;

    /**
     * Checks, whether the current design uses Twitter Bootstrap or not
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->designXml = simplexml_load_file($appPath->getDesignPathInternal() . 'info.xml');

        if (isset($this->designXml->use_bootstrap) && (string)$this->designXml->use_bootstrap === 'true') {
            $this->enableLibraries(['bootstrap']);
        }
    }

    /**
     * @return array
     */
    public function fetchAdditionalThemeCssFiles()
    {
        if (isset($this->designXml->css) && empty($this->additionalThemeCssFiles)) {
            foreach ($this->designXml->css->item as $file) {
                $this->addCssFile($file);
            }
        }

        return $this->additionalThemeCssFiles;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function addCssFile($file)
    {
        $this->additionalThemeCssFiles[] = $file;

        return $this;
    }

    /**
     * @return array
     */
    public function fetchAdditionalThemeJsFiles()
    {
        if (isset($this->designXml->js) && empty($this->additionalThemeJsFiles)) {
            foreach ($this->designXml->js->item as $file) {
                $this->addJsFile($file);
            }
        }

        return $this->additionalThemeJsFiles;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function addJsFile($file)
    {
        $this->additionalThemeJsFiles[] = $file;

        return $this;
    }

    /**
     * Activates frontend libraries
     *
     * @param array $libraries
     *
     * @return $this
     */
    public function enableLibraries(array $libraries)
    {
        foreach ($libraries as $library) {
            if (array_key_exists($library, $this->libraries) === true) {
                // Resolve javascript library dependencies recursively
                if (!empty($this->libraries[$library]['dependencies'])) {
                    $this->enableLibraries($this->libraries[$library]['dependencies']);
                }

                // Enabled the javascript library
                $this->libraries[$library]['enabled'] = true;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getLibraries()
    {
        return $this->libraries;
    }

    /**
     * @return string
     */
    public function getEnabledLibrariesAsString()
    {
        if (empty($this->enabledLibraries)) {
            $enabledLibraries = [];
            foreach ($this->libraries as $library => $values) {
                if ($values['enabled'] === true) {
                    $enabledLibraries[] = $library;
                }
            }

            $this->enabledLibraries = implode(',', $enabledLibraries);
        }

        return $this->enabledLibraries;
    }
}
