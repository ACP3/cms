<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\EventListener;

use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Authentication\Exception\UnauthorizedAccessException;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Helpers\RedirectMessages;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IsUserAuthenticatedListener implements EventSubscriberInterface
{
    public function __construct(private readonly RequestInterface $request, private readonly RedirectMessages $redirectMessages, private readonly Translator $translator, private readonly UserModelInterface $user)
    {
    }

    /**
     * @throws \ACP3\Core\Authentication\Exception\UnauthorizedAccessException
     */
    public function __invoke(ControllerActionBeforeDispatchEvent $event): void
    {
        if ($event->getArea() === AreaEnum::AREA_ADMIN && $this->user->isAuthenticated() === false) {
            $this->redirectMessages->setMessage(
                false,
                $this->translator->t('users', 'authentication_required')
            );

            throw new UnauthorizedAccessException(['redirect' => base64_encode($this->request->getPathInfo())]);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerActionBeforeDispatchEvent::class => ['__invoke', 255],
        ];
    }
}
