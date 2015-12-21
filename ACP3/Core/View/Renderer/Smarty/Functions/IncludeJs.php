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
     * @var \ACP3\Core\Assets\FileResolver
     */
    protected $fileResolver;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var array
     */
    protected $alreadyIncluded = [];

    /**
     * @param \ACP3\Core\Assets                      $assets
     * @param \ACP3\Core\Assets\FileResolver         $fileResolved
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(
        Core\Assets $assets,
        Core\Assets\FileResolver $fileResolved,
        Core\Environment\ApplicationPath $appPath
    )
    {
        $this->assets = $assets;
        $this->fileResolver = $fileResolved;
        $this->appPath = $appPath;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
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

                $path = $this->fileResolver->getStaticAssetPath($module . '/Resources/', $module . '/', 'Assets/js', $file . '.js');

                if (strpos($path, '/ACP3/Modules/') !== false) {
                    $path = $this->appPath->getWebRoot() . substr($path, strpos($path, '/ACP3/Modules/') + 1);
                } else {
                    $path = $this->appPath->getWebRoot() . substr($path, strlen(ACP3_ROOT_DIR));
                }
                return sprintf($script, $path);
            }
            return '';
        }

        throw new \Exception('Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!');
    }
}
