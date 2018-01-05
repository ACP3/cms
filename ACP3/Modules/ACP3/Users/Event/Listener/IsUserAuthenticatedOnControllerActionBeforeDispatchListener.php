<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Event\Listener;

use ACP3\Core\Authentication\Exception\UnauthorizedAccessException;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Helpers\RedirectMessages;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class IsUserAuthenticatedOnControllerActionBeforeDispatchListener
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var UserModel
     */
    private $user;
    /**
     * @var RedirectMessages
     */
    private $redirectMessages;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * IsUserAuthenticatedOnControllerActionBeforeDispatchListener constructor.
     * @param RequestInterface $request
     * @param RedirectMessages $redirectMessages
     * @param TranslatorInterface $translator
     * @param UserModel $user
     */
    public function __construct(
        RequestInterface $request,
        RedirectMessages $redirectMessages,
        TranslatorInterface $translator,
        UserModel $user
    ) {
        $this->request = $request;
        $this->user = $user;
        $this->redirectMessages = $redirectMessages;
        $this->translator = $translator;
    }

    public function isUserAuthenticated()
    {
        if ($this->request->getArea() === AreaEnum::AREA_ADMIN && $this->user->isAuthenticated() === false) {
            $this->redirectMessages->setMessage(
                false,
                $this->translator->t('users', 'authentication_required')
            );

            throw new UnauthorizedAccessException();
        }
    }
}
