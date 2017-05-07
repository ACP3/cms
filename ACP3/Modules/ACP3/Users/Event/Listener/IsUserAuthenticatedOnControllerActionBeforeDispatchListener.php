<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Event\Listener;


use ACP3\Core\Authentication\Exception\UnauthorizedAccessException;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
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
     * IsUserAuthenticatedOnControllerActionBeforeDispatchListener constructor.
     * @param RequestInterface $request
     * @param UserModel $user
     */
    public function __construct(RequestInterface $request, UserModel $user)
    {
        $this->request = $request;
        $this->user = $user;
    }

    public function isUserAuthenticated()
    {
        if ($this->request->getArea() === AreaEnum::AREA_ADMIN && $this->user->isAuthenticated() === false) {
            throw new UnauthorizedAccessException();
        }
    }
}
