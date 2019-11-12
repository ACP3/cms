<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Core\Authentication;

use ACP3\Core\Authentication\AuthenticationInterface;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Session\SessionHandlerInterface;
use ACP3\Modules\ACP3\Users\Model\AuthenticationModel;
use ACP3\Modules\ACP3\Users\Model\Repository\UserRepository;

class Native implements AuthenticationInterface
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Session\SessionHandlerInterface
     */
    private $sessionHandler;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        RequestInterface $request,
        SessionHandlerInterface $sessionHandler,
        UserRepository $userRepository
    ) {
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function authenticate(UserModelInterface $userModel): void
    {
        $userData = null;
        if ($this->sessionHandler->has(AuthenticationModel::AUTH_NAME)) {
            $userData = $this->sessionHandler->get(AuthenticationModel::AUTH_NAME);
        } elseif ($this->request->getCookies()->has(AuthenticationModel::AUTH_NAME)) {
            [$userId, $token] = \explode('|', $this->request->getCookies()->get(AuthenticationModel::AUTH_NAME, ''));

            $userData = $this->verifyCredentials($userId, $token);
        }

        if ($userData !== null) {
            $userModel
                ->setIsAuthenticated(true)
                ->setUserId($userData['id'])
                ->setIsSuperUser($userData['super_user']);
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function verifyCredentials(int $userId, string $token): ?array
    {
        $user = $this->userRepository->getOneById($userId);
        if (!empty($user) && $user['remember_me_token'] === $token) {
            return $user;
        }

        return null;
    }
}
