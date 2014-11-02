<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core;

/**
 * Class IncludeJs
 * @package ACP3\Core\View\Renderer\Smarty
 */
class IncludeJs extends AbstractPlugin
{
    /**
     * @var Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Modules\Minify\Helpers
     */
    protected $minifyHelper;
    /**
     * @var array
     */
    protected $alreadyIncluded = array();
    /**
     * @var string
     */
    protected $pluginName = 'include_js';

    public function __construct(
        Core\View $view,
        \ACP3\Modules\Minify\Helpers $minifyHelper
    )
    {
        $this->view = $view;
        $this->minifyHelper = $minifyHelper;
    }

    /**
     * @param $params
     *
     * @throws \Exception
     * @return string
     */
    public function process($params)
    {
        if (isset($params['module'], $params['file']) === true &&
            (bool)preg_match('=/=', $params['module']) === false &&
            (bool)preg_match('=\./=', $params['file']) === false
        ) {
            // Do not include the same file multiple times
            $key = $params['module'] . '/' . $params['file'];
            if (isset($alreadyIncluded[$key]) === false) {
                if (!empty($params['depends'])) {
                    $this->view->enableJsLibraries(explode(',', $params['depends']));
                }

                $this->alreadyIncluded[$key] = true;

                $script = '<script type="text/javascript" src="%s"></script>';
                $module = ucfirst($params['module']);
                $file = $params['file'];

                $path = $this->minifyHelper->getStaticAssetPath(MODULES_DIR . $module . '/Resources/Assets/', DESIGN_PATH_INTERNAL . $module . '/', 'js', $file . '.js');
                return sprintf($script, ROOT_DIR . substr($path, strpos($path, '/ACP3/Modules')));
            }
            return '';
        }

        throw new \Exception('Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!');
    }
}