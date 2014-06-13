<?php

namespace ACP3\Modules\Minify\Controller;

use ACP3\Core;

/**
 * Minify Index Controller
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{

    public function actionIndex()
    {
        $this->setNoOutput(true);

        if (!empty($this->uri->group)) {
            $libraries = !empty($this->uri->libraries) ? explode(',', $this->uri->libraries) : array();
            $layout = isset($this->uri->layout) && !preg_match('=/=', $this->uri->layout) ? $this->uri->layout : 'layout';

            $options = array();
            switch ($this->uri->group) {
                case 'css':
                    $files = $this->_includeCssFiles($libraries, $layout);
                    break;
                case 'js':
                    $files = $this->_includeJsFiles($libraries, $layout);
                    break;
                default:
                    $files = array();
            }
            $options['files'] = $files;
            $options['maxAge'] = CONFIG_CACHE_MINIFY;
            $options['minifiers']['text/css'] = array('Minify_CSSmin', 'minify');

            \Minify::setCache(new \Minify_Cache_File(UPLOADS_DIR . 'cache/minify/', true));
            \Minify::serve('Files', $options);
        }
    }

    /**
     *
     * @param string $libraries
     * @param string $layout
     * @return array
     */
    private function _includeCssFiles($libraries, $layout)
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
            $css[] = $this->_getStaticAssetPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery-ui.css');
        }
        if (in_array('timepicker', $libraries)) {
            $css[] = $this->_getStaticAssetPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery-timepicker.css');
        }
        if (in_array('fancybox', $libraries)) {
            $css[] = $this->_getStaticAssetPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery-fancybox.css');
        }
        if (in_array('datatables', $libraries)) {
            $css[] = $this->_getStaticAssetPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery-datatables.css');
        }

        // Stylesheet für das Layout-Tenplate
        $css[] = self::_getStaticAssetPath(MODULES_DIR . 'System/', DESIGN_PATH_INTERNAL . 'System/', 'css', 'style.css');
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
        $modules = Core\Modules::getActiveModules();
        foreach ($modules as $module) {
            $systemPath = MODULES_DIR . $module['dir'] . '/';
            $designPath = DESIGN_PATH_INTERNAL . $module['dir'] . '/';
            if (true == ($stylesheet = $this->_getStaticAssetPath($systemPath, $designPath, 'css', 'style.css')) &&
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
    private function _includeJsFiles($libraries, $layout)
    {
        $xml = simplexml_load_file(DESIGN_PATH_INTERNAL . 'info.xml');

        $scripts = array();
        $scripts[] = $this->_getStaticAssetPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery.min.js');

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
            $scripts[] = $this->_getStaticAssetPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'bootbox.min.js');
        }
        if (in_array('jquery-ui', $libraries)) {
            $scripts[] = $this->_getStaticAssetPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery-ui.min.js');
        }
        if (in_array('timepicker', $libraries)) {
            $scripts[] = $this->_getStaticAssetPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery.timepicker.js');
        }
        if (in_array('fancybox', $libraries)) {
            $scripts[] = $this->_getStaticAssetPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery.fancybox.min.js');
        }
        if (in_array('datatables', $libraries)) {
            $scripts[] = $this->_getStaticAssetPath(LIBRARIES_DIR, DESIGN_PATH_INTERNAL, 'js', 'jquery.datatables.min.js');
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
     * @param $systemPath
     * @param $designPath
     * @param $dir
     * @param $file
     * @return string
     */
    protected function _getStaticAssetPath($systemPath, $designPath, $dir, $file)
    {
        $dir = !empty($dir) ? $dir . '/' : '';
        if (is_file($designPath . $dir . $file) === true) {
            return $designPath . $dir . $file;
        } elseif (is_file($systemPath . $dir . $file) === true) {
            return $systemPath . $dir . $file;
        }
        return '';
    }
}
