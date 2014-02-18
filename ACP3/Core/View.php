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
     * Nicht ausgeben
     */
    protected $noOutput = false;

    /**
     * Der auszugebende Content-Type der Seite
     *
     * @var string
     */
    protected $contentType = 'Content-Type: text/html; charset=UTF-8';

    /**
     * Das zuverwendende Seitenlayout
     *
     * @var string
     */
    protected $layout = 'layout.tpl';

    /**
     * Das zuverwendende Template für den Contentbereich
     *
     * @var string
     */
    protected $contentTemplate = '';

    /**
     * Der auszugebende Seiteninhalt
     *
     * @var string
     */
    protected $content = '';

    /**
     *
     * @var string
     */
    protected $contentAppend = '';

    /**
     * Legt fest, welche JavaScript Bibliotheken beim Seitenaufruf geladen werden sollen
     *
     * @var array
     */
    protected $jsLibraries = array('bootbox' => false, 'fancybox' => false, 'jquery-ui' => false, 'timepicker' => false, 'datatables' => false);
    /**
     * @var null
     */
    protected static $rendererObject = null;

    /**
     * Setter Methode für die $this->no_output Variable
     *
     * @param boolean $value
     * @return $this
     */
    public function setNoOutput($value)
    {
        $this->noOutput = (bool)$value;

        return $this;
    }

    /**
     * Gibt zurück, ob die Seitenausgabe mit Hilfe der Bootstraping-Klasse
     * erfolgen soll oder die Datei dies selber handelt
     *
     * @return string
     */
    public function getNoOutput()
    {
        return $this->noOutput;
    }

    /**
     * Weist der aktuell auszugebenden Seite den Content-Type zu
     *
     * @param string $data
     * @return $this
     */
    public function setContentType($data)
    {
        $this->contentType = $data;

        return $this;
    }

    /**
     * Gibt den Content-Type der anzuzeigenden Seiten zurück
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Weist der aktuell auszugebenden Seite ein Layout zu
     *
     * @param string $file
     * @return $this
     */
    public function setLayout($file)
    {
        $this->layout = $file;

        return $this;
    }

    /**
     * Gibt das aktuell zugewiesene Layout zurück
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Setzt das Template für den Contentbereich der Seite
     *
     * @param string $file
     * @return $this
     */
    public function setContentTemplate($file)
    {
        $this->contentTemplate = $file;

        return $this;
    }

    /**
     * Gibt das aktuell zugewiesene Template für den Contentbereich zurück
     *
     * @return string
     */
    public function getContentTemplate()
    {
        return $this->contentTemplate;
    }

    /**
     * Weist dem Template den auszugebenden Inhalt zu
     *
     * @param string $data
     * @return $this
     */
    public function setContent($data)
    {
        $this->content = $data;

        return $this;
    }

    /**
     * Fügt weitere Daten an den Seiteninhalt an
     *
     * @param string $data
     * @return $this
     */
    public function appendContent($data)
    {
        $this->contentAppend .= $data;

        return $this;
    }

    /**
     * Gibt den auszugebenden Seiteninhalt zurück
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Gibt die anzuhängenden Inhalte an den Seiteninhalt zurück
     *
     * @return string
     */
    public function getContentAppend()
    {
        return $this->contentAppend;
    }

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
     * @return string
     */
    public function buildMinifyLink()
    {
        $minify = ROOT_DIR . 'libraries/min/' . ((bool)CONFIG_SEO_MOD_REWRITE === true && defined('IN_ADM') === false ? '' : '?') . 'g=%s&amp;' . CONFIG_DESIGN;

        ksort($this->jsLibraries);
        $libraries = '';
        foreach ($this->jsLibraries as $library => $enable) {
            if ($enable === true) {
                $libraries .= $library . ',';
            }
        }

        if ($libraries !== '') {
            $minify .= '&amp;libraries=' . substr($libraries, 0, -1);
        }

        return $minify;
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
        $css[] = DESIGN_PATH_INTERNAL . 'common.css';
        $css[] = DESIGN_PATH_INTERNAL . (is_file(DESIGN_PATH_INTERNAL . $layout . '.css') === true ? $layout : 'layout') . '.css';

        // Zusätzliche Stylesheets einbinden
        $extra_css = explode(',', CONFIG_EXTRA_CSS);
        if (count($extra_css) > 0) {
            foreach ($extra_css as $file) {
                $path = DESIGN_PATH_INTERNAL . 'css/' . trim($file);
                if (is_file($path) && in_array($path, $css) === false) {
                    $css[] = $path;
                }
            }
        }

        // Stylesheets der Module
        $modules = \ACP3\Core\Modules::getActiveModules();
        foreach ($modules as $module) {
            $systemPath = MODULES_DIR . $module['dir'] . '/View/';
            $designPath = DESIGN_PATH_INTERNAL . strtolower($module['dir']) . '/';
            if (true == ($stylesheet = self::getCssJsPath($systemPath, $designPath, '', 'style.css'))) {
                $css[] = $stylesheet;
            }
            // Append some custom styles to the default module styling
            $path_module_append = $designPath . 'append.css';
            if (is_file($path_module_append) === true) {
                $css[] = $path_module_append;
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
        $extra_js = explode(',', CONFIG_EXTRA_JS);
        if (count($extra_js) > 0) {
            foreach ($extra_js as $file) {
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
     * @param mixed $cache_id
     * @param null $compile_id
     * @param null $parent
     * @internal param int $cache_lifetime
     */
    public function displayTemplate($template, $cache_id = null, $compile_id = null, $parent = null)
    {
        echo $this->fetchTemplate($template, $cache_id, $compile_id, $parent, true);
    }

    /**
     * Gibt ein Template aus
     *
     * @param string $template
     * @param mixed $cache_id
     * @param mixed $compile_id
     * @param object $parent
     * @param boolean $display
     * @throws \Exception
     * @return string
     */
    public function fetchTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $display = false)
    {
        if ($this->templateExists($template)) {
            return self::$rendererObject->fetch($template, $cache_id, $compile_id, $parent, $display);
        } else {
            // Pfad zerlegen
            $fragments = explode('/', $template);
            $fragments[0] = ucfirst($fragments[0]);
            $path = $fragments[0] . '/View/' . $fragments[1];
            if (count($fragments) > 1 && $this->templateExists($path)) {
                return self::$rendererObject->fetch($path, $cache_id, $compile_id, $parent, $display);
            } else {
                throw new \Exception("The requested template " + $template + " can't be found!");
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
