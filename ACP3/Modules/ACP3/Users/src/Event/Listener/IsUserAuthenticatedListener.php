<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Event\Listener;

use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Authentication\Exception\UnauthorizedAccessException;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Helpers\RedirectMessages;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class IsUserAuthenticatedListener
{
    /**
     * @var UserModel
     */
    private $user;
    /**
     * @var RedirectMessages
     */
    private $redirectMessages;
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;

    /**
     * IsUserAuthenticatedOnControllerActionBeforeDispatchListener constructor.
     */
    public function __construct(
        RequestInterface $request,
        RedirectMessages $redirectMessages,
        Translator $translator,
        UserModel $user
    ) {
        $this->user = $user;
        $this->redirectMessages = $redirectMessages;
        $this->translator = $translator;
        $this->request = $request;
    }

    /**
     * @throws \ACP3\Core\Authentication\Exception\UnauthorizedAccessException
     */
    public function __invoke(ControllerActionBeforeDispatchEvent $event)
    {
        if ($event->getArea() === AreaEnum::AREA_ADMIN && $this->user->isAuthenticated() === false) {
            $this->redirectMessages->setMessage(
                false,
                $this->translator->t('users', 'authentication_required')
            );

            throw new UnauthorizedAccessException(['redirect' => \base64_encode($this->request->getPathInfo())]);
        }
    }
}
