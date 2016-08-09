<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Application\BootstrapCache;


use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Esi extends \Symfony\Component\HttpKernel\HttpCache\Esi
{
    /**
     * @inheritdoc
     */
    public function process(Request $request, Response $response)
    {
        if ($response instanceof BinaryFileResponse) {
            return $response;
        }

        return parent::process($request, $response);
    }
}
