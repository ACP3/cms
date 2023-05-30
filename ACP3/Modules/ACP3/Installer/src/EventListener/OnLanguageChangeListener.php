<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\EventListener;

use ACP3\Core\Application\Event\ControllerActionRequestEvent;
use ACP3\Core\Http\RedirectResponse;
use ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;

class OnLanguageChangeListener implements EventSubscriberInterface
{
    public function __construct(private readonly ApplicationPath $applicationPath, private readonly RedirectResponse $redirect)
    {
    }

    /**
     * If the language has been changed, set a cookie with the new default language and force a page reload.
     */
    public function __invoke(ControllerActionRequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getPost()->has('lang')) {
            $response = $this->redirect->toNewPage($this->applicationPath->getPhpSelf() . '/' . $request->getFullPath());

            $response->headers->setCookie(
                new Cookie(
                    'ACP3_INSTALLER_LANG',
                    $request->getPost()->get('lang', ''),
                    time() + 3600,
                    '/'
                )
            );

            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerActionRequestEvent::class => '__invoke',
        ];
    }
}
