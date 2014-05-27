<?php

namespace ACP3\Core;

/**
 * Klasse für die Ausgabe der Seite
 *
 * @author Tino Goratsch
 */
class View
{
    /**
     * Legt fest, welche JavaScript Bibliotheken beim Seitenaufruf geladen werden sollen
     *
     * @var array
     */
    protected $jsLibraries = array(
        'bootbox' => false,
        'fancybox' => false,
        'jquery-ui' => false,
        'timepicker' => false,
        'datatables' => false
    );
    /**
     * @var string
     */
    protected $jsLibrariesCache = '';

    /**
     * @var
     */
    protected static $rendererObject;

    /**
     * Gets the renderer
     *
     * @return object
     */
    public static function getRenderer()
    {
        return self::$rendererObject;
    }

    /**
     * Aktiviert einzelne JavaScript Bibliotheken
     *
     * @param array $libraries
     * @return $this
     */
    public function enableJsLibraries(array $libraries)
    {
        foreach ($libraries as $library) {
            if (array_key_exists($library, $this->jsLibraries) === true) {
                $this->jsLibraries[$library] = true;
                if ($library === 'timepicker') {
                    $this->jsLibraries['jquery-ui'] = true;
                }
            }
        }

        return $this;
    }

    /**
     * Set the disered renderer with an optional config array
     *
     * @param string $renderer
     * @param array $params
     * @throws \Exception
     */
    public static function factory($renderer = 'Smarty', array $params = array())
    {
        $path = CLASSES_DIR . 'View/Renderer/' . $renderer . '.php';
        if (is_file($path) === true) {
            $className = "\\ACP3\\Core\\View\\Renderer\\{$renderer}";
            self::$rendererObject = new $className($params);
        } else {
            throw new \Exception('File ' . $path . ' not found!');
        }
    }

    /**
     * Erstellt den Link zum Minifier mitsamt allen zu ladenden JavaScript Bibliotheken
     *
     * @param $group
     * @param string $layout
     * @return string
     */
    public function buildMinifyLink($group, $layout = '')
    {
        if (!empty($layout)) {
            $layout = '/layout_' . $layout;
        }

        $libraries = $this->_getJsLibrariesCache();

        if ($libraries !== '') {
            $libraries = '/libraries_' . substr($libraries, 0, -1);
        }

        return Registry::get('URI')->route('minify/index/index/group_' . $group . '/design_' . CONFIG_DESIGN . $layout . $libraries);
    }

    /**
     * @return string
     */
    private function _getJsLibrariesCache()
    {
        if (empty($this->jsLibrariesCache)) {
            ksort($this->jsLibraries);
            foreach ($this->jsLibraries as $library => $enable) {
                if ($enable === true) {
                    $this->jsLibrariesCache .= $library . ',';
                }
            }
        }

        return $this->jsLibrariesCache;

    }

    /**
     * @param $systemPath
     * @param $designPath
     * @param $dir
     * @param $file
     * @return string
     */
    protected static function getCssJsPath($systemPath, $designPath, $dir, $file)
    {
        $dir = !empty($dir) ? $dir . '/' : '';
        if (is_file($designPath . $dir . $file) === true) {
            return $designPath . $dir . $file;
        } elseif (is_file($systemPath . $dir . $file) === true) {
            return $systemPath . $dir . $file;
        }
        return '';
    }

    /**
     *
     * @param string $libraries
     * @param string $layout
     * @return array
     */
    public static function includeCssFiles($libraries, $layout)
    {
        $xml = simplexml_load_file(DESIGN_PATH_INTERNAL . 'info.xml');

        $css = array();

        if (isset($xml->use_bootstrap) && (string)$xml->use_bootstrap === 'true') {
            $css[] = LIBRARIES_DIR . 'bootstrap/css/bootstrap.min.css';
        }

        if (isset($xml->css)) {
            foreach ($xml->css->item as $file) {
                $path = DESIGN_PATH_INTERNAL . 'css/' . $file;
                if (is_file($path) === true) {
                    $css[] = $path;
                }
            }
        }

        // Stylesheets der Bibliotheken zuerst laden,
        // damit deren Styles überschrieben werden können
        if (in_array('jquery-ui', $libraries)) {
            $css[] = self::getCssJsPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery-ui.css');
        }
        if (in_array('timepicker', $libraries)) {
            $css[] = self::getCssJsPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery-timepicker.css');
        }
        if (in_array('fancybox', $libraries)) {
            $css[] = self::getCssJsPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery-fancybox.css');
        }
        if (in_array('datatables', $libraries)) {
            $css[] = self::getCssJsPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery-datatables.css');
        }

        // Stylesheet für das Layout-Tenplate
        $css[] = self::getCssJsPath(MODULES_DIR . 'System/', DESIGN_PATH_INTERNAL . 'system/', 'css', 'style.css');
        $css[] = DESIGN_PATH_INTERNAL . (is_file(DESIGN_PATH_INTERNAL . $layout . '.css') === true ? $layout : 'layout') . '.css';

        // Zusätzliche Stylesheets einbinden
        $extraCss = explode(',', CONFIG_EXTRA_CSS);
        if (count($extraCss) > 0) {
            foreach ($extraCss as $file) {
                $path = DESIGN_PATH_INTERNAL . 'css/' . trim($file);
                if (is_file($path) && in_array($path, $css) === false) {
                    $css[] = $path;
                }
            }
        }

        // Stylesheets der Module
        $modules = Modules::getActiveModules();
        foreach ($modules as $module) {
            $systemPath = MODULES_DIR . $module['dir'] . '/';
            $designPath = DESIGN_PATH_INTERNAL . strtolower($module['dir']) . '/';
            if (true == ($stylesheet = self::getCssJsPath($systemPath, $designPath, 'css', 'style.css')) &&
                $module['dir'] !== 'System'
            ) {
                $css[] = $stylesheet;
            }
            // Append some custom styles to the default module styling
            $pathModuleAppend = $designPath . 'append.css';
            if (is_file($pathModuleAppend) === true) {
                $css[] = $pathModuleAppend;
            }
        }

        return $css;
    }

    /**
     *
     * @param string $libraries
     * @param string $layout
     * @return array
     */
    public static function includeJsFiles($libraries, $layout)
    {
        $xml = simplexml_load_file(DESIGN_PATH_INTERNAL . 'info.xml');

        $scripts = array();
        $scripts[] = self::getCssJsPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery.min.js');

        if (isset($xml->use_bootstrap) && (string)$xml->use_bootstrap === 'true') {
            $scripts[] = LIBRARIES_DIR . 'bootstrap/js/bootstrap.min.js';
        }

        // Include js files from the design
        if (isset($xml->js)) {
            foreach ($xml->js->item as $js) {
                $path = DESIGN_PATH_INTERNAL . 'js/' . $js;
                if (is_file($path) === true) {
                    $scripts[] = $path;
                }
            }
        }

        // JS-Libraries to include
        if (in_array('bootbox', $libraries)) {
            $scripts[] = self::getCssJsPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'bootbox.min.js');
        }
        if (in_array('jquery-ui', $libraries)) {
            $scripts[] = self::getCssJsPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery-ui.min.js');
        }
        if (in_array('timepicker', $libraries)) {
            $scripts[] = self::getCssJsPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery.timepicker.js');
        }
        if (in_array('fancybox', $libraries)) {
            $scripts[] = self::getCssJsPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery.fancybox.min.js');
        }
        if (in_array('datatables', $libraries)) {
            $scripts[] = self::getCssJsPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery.datatables.min.js');
        }

        // Include general js file of the layout
        if (is_file(DESIGN_PATH_INTERNAL . $layout . '.js') === true) {
            $scripts[] = DESIGN_PATH_INTERNAL . $layout . '.js';
        }

        // Include additional js files from the system config
        $extraJs = explode(',', CONFIG_EXTRA_JS);
        if (count($extraJs) > 0) {
            foreach ($extraJs as $file) {
                $path = DESIGN_PATH_INTERNAL . 'js/' . trim($file);
                if (is_file($path) && in_array($path, $scripts) === false) {
                    $scripts[] = $path;
                }
            }
        }

        return $scripts;
    }

    /**
     * Gibt ein Template direkt aus
     *
     * @param string $template
     * @param mixed $cacheId
     * @param null $compileId
     * @param null $parent
     * @internal param int $cache_lifetime
     */
    public function displayTemplate($template, $cacheId = null, $compileId = null, $parent = null)
    {
        echo $this->fetchTemplate($template, $cacheId, $compileId, $parent, true);
    }

    /**
     * Gibt ein Template aus
     *
     * @param string $template
     * @param mixed $cacheId
     * @param mixed $compileId
     * @param object $parent
     * @param boolean $display
     * @throws \Exception
     * @return string
     */
    public function fetchTemplate($template, $cacheId = null, $compileId = null, $parent = null, $display = false)
    {
        if ($this->templateExists($template)) {
            return self::$rendererObject->fetch($template, $cacheId, $compileId, $parent, $display);
        } else {
            // Pfad zerlegen
            $fragments = explode('/', $template);
            $fragments[0] = ucfirst($fragments[0]);

            if (count($fragments) === 3) {
                $path = $fragments[0] . '/View/' . $fragments[1] . '/' . $fragments[2];
            } else {
                $path = $fragments[0] . '/View/' . $fragments[1];
            }

            if (count($fragments) > 1 && $this->templateExists($path)) {
                return self::$rendererObject->fetch($path, $cacheId, $compileId, $parent, $display);
            } else {
                throw new \Exception("The requested template " . $template . " can't be found!");
            }
        }
    }

    /**
     * Checks, whether a templates exists or not
     *
     * @param string $template
     * @return boolean
     */
    public function templateExists($template)
    {
        return self::$rendererObject->templateExists($template);
    }

    /**
     * Weist dem View-Object eine Template-Variable zu
     *
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    public function assign($name, $value = null)
    {
        return self::$rendererObject->assign($name, $value);
    }

}
