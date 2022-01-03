<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @method array|string|Response|void|null __invoke(...$_ = null)
 */
interface InvokableActionInterface
{
    public function preDispatch(): void;

    public function display(array|string|Response|null $actionResult): Response;
}
