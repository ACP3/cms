<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class RedirectMessages
{
    public function __construct(private readonly Core\Http\RedirectResponse $redirect, private readonly Core\Http\RequestInterface $request, private readonly Session $sessionHandler)
    {
    }

    /**
     * Gets the generated redirect message from setMessage().
     *
     * @return array<string, string[]>
     *
     * @throws \Exception
     */
    public function getMessage(): array
    {
        return $this->sessionHandler->getFlashBag()->all();
    }

    /**
     * Sets a redirect messages and redirects to the given internal path.
     */
    public function setMessage(bool $isSuccess, string $text, string $path = null): JsonResponse|RedirectResponse
    {
        $this->sessionHandler->getFlashBag()
            ->set($isSuccess ? 'success' : 'error', $text);

        // If no path has been given, guess it automatically
        if ($path === null) {
            $path = $this->request->getModuleAndController();
        }

        return $this->redirect->temporary($path);
    }
}
