<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

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
     * @param \ACP3\Core\Assets\FileResolver         $fileResolver
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(
        Core\Assets $assets,
        Core\Assets\FileResolver $fileResolver,
        Core\Environment\ApplicationPath $appPath
    ) {
        $this->assets = $assets;
        $this->fileResolver = $fileResolver;
        $this->appPath = $appPath;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        if (!empty($params['depends'])) {
            $this->assets->enableLibraries(\explode(',', $params['depends']));
        }

        if ($this->hasValidParams($params)) {
            $key = $params['module'] . '/' . $params['file'];

            // Do not include the same file multiple times
            if (isset($this->alreadyIncluded[$key]) === false) {
                $this->alreadyIncluded[$key] = true;

                return \sprintf(
                    '<script defer src="%s"></script>',
                    $this->resolvePath($params) . '?v=' . Core\Application\BootstrapInterface::VERSION
                );
            }

            return '';
        }

        if (empty($params['depends'])) {
            throw new \Exception('Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!');
        }

        return '';
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    private function hasValidParams(array $params)
    {
        return isset($params['module'], $params['file']) === true &&
            (bool) \preg_match('=/=', $params['module']) === false &&
            (bool) \preg_match('=\./=', $params['file']) === false;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    protected function resolvePath(array $params)
    {
        $module = \ucfirst($params['module']);
        $file = $params['file'];

        $path = $this->fileResolver->getStaticAssetPath(
            $module . '/Resources/',
            $module . '/',
            'Assets/js',
            $file . '.js'
        );

        if (\strpos($path, '/ACP3/Modules/') !== false) {
            $path = $this->appPath->getWebRoot() . \substr($path, \strpos($path, '/ACP3/Modules/') + 1);
        } else {
            $path = $this->appPath->getWebRoot() . \substr($path, \strlen(ACP3_ROOT_DIR));
        }

        return $path;
    }
}
