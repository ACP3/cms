<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @method mixed[]|string|Response|void|null __invoke(...$_ = null)
 */
interface InvokableActionInterface
{
    public function preDispatch(): void;

    /**
     * @param array<string, mixed>|string|null $actionResult
     */
    public function display(array|string|null $actionResult): Response;
}
