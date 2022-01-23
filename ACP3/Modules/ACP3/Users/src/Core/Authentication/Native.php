<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Core\Authentication;

use ACP3\Core\Authentication\AuthenticationInterface;
use ACP3\Core\Authentication\Model\AuthenticationModelInterface;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Users\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class Native implements AuthenticationInterface
{
    public function __construct(private RequestInterface $request, private Session $sessionHandler, private UserRepository $userRepository)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function authenticate(UserModelInterface $userModel): void
    {
        $userData = null;
        if ($this->sessionHandler->has(AuthenticationModelInterface::AUTH_NAME)) {
            $userData = $this->sessionHandler->get(AuthenticationModelInterface::AUTH_NAME);
        } elseif ($this->request->getCookies()->has(AuthenticationModelInterface::AUTH_NAME)) {
            [$userId, $token] = explode('|', $this->request->getCookies()->get(AuthenticationModelInterface::AUTH_NAME, ''));

            $userData = $this->verifyCredentials((int) $userId, $token);
        }

        if ($userData !== null) {
            $userModel
                ->setIsAuthenticated(true)
                ->setUserId($userData['id'])
                ->setIsSuperUser($userData['super_user']);
        }
    }

    /**
     * @return array<string, mixed>|null
     *
     * @throws \Doctrine\DBAL\Exception
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
