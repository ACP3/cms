<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Core\Authentication;

use ACP3\Core\Authentication\AuthenticationInterface;
use ACP3\Core\Authentication\Exception\AuthenticationException;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Session\SessionHandlerInterface;
use ACP3\Modules\ACP3\Users\Model\AuthenticationModel;
use ACP3\Modules\ACP3\Users\Model\Repository\UserRepository;

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
     * @var AuthenticationModel
     */
    protected $authenticationModel;
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Native constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Session\SessionHandlerInterface $sessionHandler
     * @param AuthenticationModel $authenticationModel
     * @param UserRepository $userRepository
     */
    public function __construct(
        RequestInterface $request,
        SessionHandlerInterface $sessionHandler,
        AuthenticationModel $authenticationModel,
        UserRepository $userRepository)
    {
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
        $this->authenticationModel = $authenticationModel;
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritdoc
     */
    public function authenticate()
    {
        $userData = 0;
        if ($this->sessionHandler->has(AuthenticationModel::AUTH_NAME)) {
            $userData = $this->sessionHandler->get(AuthenticationModel::AUTH_NAME, []);
        } elseif ($this->request->getCookies()->has(AuthenticationModel::AUTH_NAME)) {
            list($userId, $token) = explode('|', $this->request->getCookies()->get(AuthenticationModel::AUTH_NAME, ''));

            $userData = $this->verifyCredentials($userId, $token);
        }

        $this->authenticationModel->authenticate($userData);
    }

    /**
     * @param int $userId
     * @param string $token
     * @return array|int
     * @throws AuthenticationException
     */
    protected function verifyCredentials($userId, $token)
    {
        $user = $this->userRepository->getOneById($userId);
        if (!empty($user) && $user['remember_me_token'] === $token) {
            return $user;
        }

        return 0;
    }
}
