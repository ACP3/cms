<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Exception;

interface ForwardControllerActionAwareExceptionInterface extends \Throwable
{
    /**
     * Returns the serviceId of the controller action to forward to.
     */
    public function getServiceId(): string;

    /**
     * @return array<string, string>
     */
    public function routeParams(): array;
}
