<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Session;

/**
 * @deprecated since 4.49.0. To be removed with 5.0.0. Typehint against \Symfony\Component\HttpFoundation\Session\SessionInterface instead
 */
interface SessionHandlerInterface
{
    public const SESSION_NAME = SessionConstants::SESSION_NAME;
    public const XSRF_TOKEN_NAME = SessionConstants::XSRF_TOKEN_NAME;
}
