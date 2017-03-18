<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;


use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Environment\ApplicationPath;

class Image extends AbstractFunction
{
    /**
     * @var FileResolver
     */
    private $fileResolver;
    /**
     * @var ApplicationPath
     */
    private $appPath;

    /**
     * Image constructor.
     * @param FileResolver $fileResolver
     * @param ApplicationPath $appPath
     */
    public function __construct(FileResolver $fileResolver, ApplicationPath $appPath)
    {
        $this->fileResolver = $fileResolver;
        $this->appPath = $appPath;
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        if (isset($params['file']) === true && (bool)preg_match('=\./=', $params['file']) === false) {
            $file = $params['file'];

            $path = $this->fileResolver->getStaticAssetPath('/', '/', 'Assets/img', $file);

            if (strpos($path, '/ACP3/Modules/') !== false) {
                $path = $this->appPath->getWebRoot() . substr($path, strpos($path, '/ACP3/Modules/') + 1);
            } else {
                $path = $this->appPath->getWebRoot() . substr($path, strlen(ACP3_ROOT_DIR));
            }

            return $path . '?v=' . BootstrapInterface::VERSION;
        }

        throw new \Exception('Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!');
    }

    /**
     * @return string
     */
    public function getExtensionName()
    {
        return 'image';
    }
}
