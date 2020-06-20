<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @method array|Response|void|null __invoke(...$params = null)
 */
interface InvokableActionInterface extends ActionInterface
{
}
