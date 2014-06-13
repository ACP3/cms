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
        if (strpos($template, '/') !== false) {
            $template = ucfirst($template);
        }

        if ($this->templateExists($template)) {
            return self::$rendererObject->fetch($template, $cacheId, $compileId, $parent, $display);
        } else {
            // Pfad zerlegen
            $fragments = explode('/', $template);

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
