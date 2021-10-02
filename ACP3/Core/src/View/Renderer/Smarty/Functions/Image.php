<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Http\RequestInterface;

class Image extends AbstractFunction
{
    /**
     * @var FileResolver
     */
    private $fileResolver;
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(RequestInterface $request, FileResolver $fileResolver)
    {
        $this->fileResolver = $fileResolver;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): string
    {
        if (isset($params['file'], $params['module']) === true && (bool) preg_match('=\./=', $params['file']) === false) {
            $file = $params['file'];

            $path = $this->fileResolver->getWebStaticAssetPath($params['module'], 'Assets/img', $file);

            if (isset($params['absolute']) && $params['absolute'] === true) {
                $path = $this->request->getScheme() . '://' . $this->request->getHttpHost() . $path;
            }

            return $path . '?v=' . BootstrapInterface::VERSION;
        }

        throw new \InvalidArgumentException(sprintf('Not all necessary arguments for the function %s were passed!', __FUNCTION__));
    }
}
