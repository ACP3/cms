<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\Formatter;

use ACP3\Core;

class RewriteInternalUri
{
    public function __construct(private readonly Core\Environment\ApplicationPath $appPath, private readonly Core\Controller\Helper\ControllerActionExists $controllerActionExists, private readonly Core\Http\RequestInterface $request, private readonly Core\Router\RouterInterface $router, private readonly Core\Validation\ValidationRules\InternalUriValidationRule $internalUriValidationRule)
    {
    }

    public function rewriteInternalUri(string $text): string
    {
        $rootDir = str_replace('/', '\/', $this->appPath->getWebRoot());
        $host = $this->request->getServer()->get('HTTP_HOST');
        $pattern = '/(<a([^>]+)href=")?(http(s?):\/\/' . $host . ')?(' . $rootDir . ')?(index\.php)?(\/?)((?i:[a-z\d_\-]+\/){2,})(")?/i';

        return preg_replace_callback(
            $pattern,
            [$this, 'rewriteInternalUriCallback'],
            $text
        );
    }

    /**
     * @param string[] $matches
     */
    private function rewriteInternalUriCallback(array $matches): string
    {
        if ($this->internalUriValidationRule->isValid($matches[8]) === true) {
            $resourceParts = explode('/', $matches[8]);
            $path = $this->getResourcePath($resourceParts);
            if ($this->controllerActionExists->controllerActionExists($path) === true) {
                if (!empty($matches[1])) {
                    return '<a' . $matches[2] . 'href="' . $this->router->route($matches[8]) . '"';
                }

                return $this->router->route($matches[8]);
            }
        }

        return $matches[0];
    }

    /**
     * @param string[] $resourceParts
     */
    private function getResourcePath(array $resourceParts): string
    {
        $path = 'frontend/' . $resourceParts[0];
        if (!empty($resourceParts[1])) {
            $path .= '/' . $resourceParts[1];
        }
        if (!empty($resourceParts[2])) {
            $path .= '/' . $resourceParts[2];
        }
        if (!empty($resourceParts[3])) {
            $path .= '/' . $resourceParts[3];
        }

        return $path;
    }
}
