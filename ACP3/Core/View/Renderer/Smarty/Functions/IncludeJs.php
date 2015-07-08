<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class IncludeJs
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class IncludeJs extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Assets
     */
    protected $assets;
    /**
     * @var \ACP3\Core\Assets\ThemeResolver
     */
    protected $themeResolver;
    /**
     * @var array
     */
    protected $alreadyIncluded = [];

    /**
     * @param \ACP3\Core\Assets               $assets
     * @param \ACP3\Core\Assets\ThemeResolver $themeResolver
     */
    public function __construct(
        Core\Assets $assets,
        Core\Assets\ThemeResolver $themeResolver
    )
    {
        $this->assets = $assets;
        $this->themeResolver = $themeResolver;
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'include_js';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        if (isset($params['module'], $params['file']) === true &&
            (bool)preg_match('=/=', $params['module']) === false &&
            (bool)preg_match('=\./=', $params['file']) === false
        ) {
            // Do not include the same file multiple times
            $key = $params['module'] . '/' . $params['file'];
            if (isset($this->alreadyIncluded[$key]) === false) {
                if (!empty($params['depends'])) {
                    $this->assets->enableLibraries(explode(',', $params['depends']));
                }

                $this->alreadyIncluded[$key] = true;

                $script = '<script type="text/javascript" src="%s"></script>';
                $module = ucfirst($params['module']);
                $file = $params['file'];

                $path = $this->themeResolver->getStaticAssetPath($module . '/Resources/', $module . '/', 'Assets/js', $file . '.js');

                if (strpos($path, '/ACP3/Modules/') !== false) {
                    $path = ROOT_DIR . substr($path, strpos($path, '/ACP3/Modules/') + 1);
                } else {
                    $path = ROOT_DIR . substr($path, strlen(ACP3_ROOT_DIR));
                }
                return sprintf($script, $path);
            }
            return '';
        }

        throw new \Exception('Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!');
    }
}
