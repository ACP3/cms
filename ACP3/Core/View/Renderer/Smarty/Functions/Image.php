<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;


use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;

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
     * @var RequestInterface
     */
    private $request;

    /**
     * Image constructor.
     * @param RequestInterface $request
     * @param FileResolver $fileResolver
     * @param ApplicationPath $appPath
     */
    public function __construct(RequestInterface $request, FileResolver $fileResolver, ApplicationPath $appPath)
    {
        $this->fileResolver = $fileResolver;
        $this->appPath = $appPath;
        $this->request = $request;
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

            if (isset($params['absolute']) && $params['absolute'] === true) {
                $path = $this->request->getScheme() . '://' . $this->request->getHttpHost() . $path;
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
