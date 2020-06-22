<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
     */
    public function __construct(RequestInterface $request, FileResolver $fileResolver, ApplicationPath $appPath)
    {
        $this->fileResolver = $fileResolver;
        $this->appPath = $appPath;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        if (isset($params['file']) === true && (bool) \preg_match('=\./=', $params['file']) === false) {
            $file = $params['file'];

            $path = $this->fileResolver->getStaticAssetPath('', 'Assets/img', $file);

            $path = $this->appPath->getWebRoot() . \substr($path, \strlen(ACP3_ROOT_DIR . DIRECTORY_SEPARATOR));

            if (isset($params['absolute']) && $params['absolute'] === true) {
                $path = $this->request->getScheme() . '://' . $this->request->getHttpHost() . $path;
            }

            return $path . '?v=' . BootstrapInterface::VERSION;
        }

        throw new \InvalidArgumentException(\sprintf('Not all necessary arguments for the function %s were passed!', __FUNCTION__));
    }
}
