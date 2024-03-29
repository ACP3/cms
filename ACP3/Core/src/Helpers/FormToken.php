<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core;
use Symfony\Component\HttpFoundation\Session\Session;

class FormToken
{
    public function __construct(protected Core\Http\RequestInterface $request, protected Session $sessionHandler)
    {
    }

    /**
     * Generates and renders the form token.
     */
    public function renderFormToken(): string
    {
        $tokenName = Core\Session\SessionConstants::XSRF_TOKEN_NAME;
        $sessionToken = $this->sessionHandler->get($tokenName);

        if (empty($sessionToken)) {
            $sessionToken = sha1(uniqid((string) mt_rand(), true));
            $this->sessionHandler->set($tokenName, $sessionToken);
        }

        return '<input type="hidden" name="' . $tokenName . '" value="' . $sessionToken . '" />';
    }

    /**
     * Removes the form token from the session.
     */
    public function unsetFormToken(string $token = ''): void
    {
        $tokenName = Core\Session\SessionConstants::XSRF_TOKEN_NAME;
        if (empty($token) && $this->request->getPost()->has($tokenName)) {
            $token = $this->request->getPost()->get($tokenName, '');
        }
        if (!empty($token)) {
            $sessionToken = $this->sessionHandler->get($tokenName);
            if (!empty($sessionToken)) {
                $this->sessionHandler->remove($tokenName);
            }
        }
    }
}
