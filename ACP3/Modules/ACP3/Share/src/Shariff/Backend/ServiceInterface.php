<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

use Psr\Http\Message\RequestInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * @param string $url
     *
     * @return RequestInterface
     */
    public function getRequest($url);

    /**
     * @return int
     */
    public function extractCount(array $data);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $content
     *
     * @return string
     */
    public function filterResponse($content);

    public function setConfig(array $config);
}
