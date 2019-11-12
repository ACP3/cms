<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Router;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;

class Router extends \ACP3\Core\Router\Router
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Core\Router\Aliases
     */
    private $aliases;

    public function __construct(
        Aliases $aliases,
        RequestInterface $request,
        ApplicationPath $appPath,
        SettingsInterface $config
    ) {
        parent::__construct($request, $appPath, $config);

        $this->aliases = $aliases;
    }

    protected function preparePath(string $path): string
    {
        $path = parent::preparePath($path);

        if ($this->isAdminUri($path) === false) {
            $path = $this->aliases->getUriAlias($path);
        }

        return $path;
    }
}
