<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\ResultResponse;

use Symfony\Component\HttpFoundation\Response;

interface ActionResultTypeInterface
{
    /**
     * @param mixed $result
     * @return bool
     */
    public function supports($result): bool;

    /**
     * @param mixed $result
     * @return Response
     */
    public function process($result): Response;
}
