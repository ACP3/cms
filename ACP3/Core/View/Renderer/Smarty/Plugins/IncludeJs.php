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
     * @var Core\Assets
     */
    protected $assets;
    /**
     * @var Core\Assets\ThemeResolver
     */
    protected $themeResolver;
    /**
     * @var array
     */
    protected $alreadyIncluded = array();
    /**
     * @var string
     */
    protected $pluginName = 'include_js';

    public function __construct(
        Core\Assets $assets,
        Core\Assets\ThemeResolver $themeResolver
    )
    {
        $this->assets = $assets;
        $this->themeResolver = $themeResolver;
    }

    /**
     * @param array $params
     * @return mixed|void
     * @throws \Exception
     */
    public function process(array $params)
    {
        if (isset($params['module'], $params['file']) === true &&
            (bool)preg_match('=/=', $params['module']) === false &&
            (bool)preg_match('=\./=', $params['file']) === false
        ) {
            // Do not include the same file multiple times
            $key = $params['module'] . '/' . $params['file'];
            if (isset($alreadyIncluded[$key]) === false) {
                if (!empty($params['depends'])) {
                    $this->assets->enableJsLibraries(explode(',', $params['depends']));
                }

                $this->alreadyIncluded[$key] = true;

                $script = '<script type="text/javascript" src="%s"></script>';
                $module = ucfirst($params['module']);
                $file = $params['file'];

                $path = $this->themeResolver->getStaticAssetPath($module . '/Resources/Assets/', $module . '/', 'js', $file . '.js');
                return sprintf($script, ROOT_DIR . substr($path, strpos($path, '/ACP3/Modules/') + 1));
            }
            return '';
        }

        throw new \Exception('Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!');
    }
}