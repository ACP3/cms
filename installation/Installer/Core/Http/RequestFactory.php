<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\Http;

use ACP3\Core\Environment\ApplicationPath;

/**
 * Class RequestFactory
 * @package ACP3\Installer\Core\Http
 */
class RequestFactory
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * RequestFactory constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * @param string $homepage
     *
     * @return \ACP3\Core\Http\RequestInterface
     */
    public function create($homepage)
    {
        $request = new Request($this->appPath);
        $request->setHomepage($homepage);
        $request->processQuery();

        return $request;
    }
}
