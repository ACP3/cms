<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Core\Authentication;

use ACP3\Core\Authentication\AuthenticationInterface;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Session\SessionHandlerInterface;
use ACP3\Modules\ACP3\Users\Model\Repository\UserRepository;
use ACP3\Modules\ACP3\Users\Model\UserModel;

/**
 * Class Native
 * @package ACP3\Modules\ACP3\Users\Core\Authentication
 */
class Native implements AuthenticationInterface
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Session\SessionHandlerInterface
     */
    protected $sessionHandler;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository
     */
    protected $userRepository;

    /**
     * Native constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface              $request
     * @param \ACP3\Core\Session\SessionHandlerInterface    $sessionHandler
     * @param \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository $userRepository
     */
    public function __construct(
        RequestInterface $request,
        SessionHandlerInterface $sessionHandler,
        UserRepository $userRepository)
    {
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritdoc
     */
    public function authenticate()
    {
        if ($this->sessionHandler->has(UserModel::AUTH_NAME)) {
            return $this->sessionHandler->get(UserModel::AUTH_NAME, []);
        } elseif ($this->request->getCookies()->has(UserModel::AUTH_NAME)) {
            list($userId, $token) = explode('|', $this->request->getCookies()->get(UserModel::AUTH_NAME, ''));

            return $this->verifyCredentials($userId, $token);
        }

        return 0;
    }

    /**
     * @param int    $userId
     * @param string $token
     *
     * @return array|int
     */
    protected function verifyCredentials($userId, $token)
    {
        $user = $this->userRepository->getOneById($userId);
        if (!empty($user) && $user['remember_me_token'] === $token) {
            return $user;
        }

        return -1;
    }
}
