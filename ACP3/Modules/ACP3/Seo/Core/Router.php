<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core;

use ACP3\Core\Config;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router\Aliases;

/**
 * Class Router
 * @package ACP3\Modules\ACP3\Seo\Core
 */
class Router extends \ACP3\Core\Router
{
    /**
     * @var \ACP3\Core\Router\Aliases
     */
    protected $aliases;

    /**
     * @param \ACP3\Core\Router\Aliases              $aliases
     * @param \ACP3\Core\Http\RequestInterface       $request
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Config                      $config
     * @param string                                 $environment
     */
    public function __construct(
        Aliases $aliases,
        RequestInterface $request,
        ApplicationPath $appPath,
        Config $config,
        $environment
    ) {
        parent::__construct($request, $appPath, $config, $environment);

        $this->aliases = $aliases;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function preparePath($path)
    {
        $path = parent::preparePath($path);

        if ($this->isAdminUri($path) === false) {
            $path = $this->aliases->getUriAlias($path);
        }

        return $path;
    }
}
