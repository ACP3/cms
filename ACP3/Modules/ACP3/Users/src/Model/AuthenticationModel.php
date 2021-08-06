<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Model;

use ACP3\Core\Authentication\Model\AuthenticationModelInterface;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Users;
use ACP3\Modules\ACP3\Users\Exception\LoginFailedException;
use ACP3\Modules\ACP3\Users\Exception\UserAccountLockedException;
use ACP3\Modules\ACP3\Users\Model\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthenticationModel implements AuthenticationModelInterface
{
    private const SALT_LENGTH = 16;
    private const REMEMBER_ME_COOKIE_LIFETIME = 31104000;

    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $sessionHandler;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository
     */
    private $userRepository;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $userModel;

    public function __construct(
        RequestInterface $request,
        ApplicationPath $appPath,
        Session $sessionHandler,
        Secure $secureHelper,
        UserModelInterface $userModel,
        UserRepository $userRepository
    ) {
        $this->request = $request;
        $this->appPath = $appPath;
        $this->sessionHandler = $sessionHandler;
        $this->secureHelper = $secureHelper;
        $this->userModel = $userModel;
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticates the user.
     */
    public function authenticate(?array $userData): void
    {
        if (\is_array($userData)) {
            $this->userModel
                ->setIsAuthenticated(true)
                ->setUserId($userData['id'])
                ->setIsSuperUser($userData['super_user']);
        }
    }

    /**
     * Logs out the current user.
     *
     * @param int $userId
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function logout($userId = 0): void
    {
        if ($userId === 0) {
            $userId = $this->userModel->getUserId();
        }

        $this->saveRememberMeToken($userId, '');
        $this->sessionHandler->invalidate();
    }

    /**
     * Setzt den internen Authentifizierungscookie.
     *
     * @param int      $userId
     * @param string   $token
     * @param int|null $expiry
     *
     * @return Cookie
     *
     * @throws \Exception
     */
    public function setRememberMeCookie($userId, $token, $expiry = null)
    {
        if ($expiry === null) {
            $expiry = static::REMEMBER_ME_COOKIE_LIFETIME;
        }

        return Cookie::create(
            self::AUTH_NAME,
            $userId . '|' . $token,
            (new \DateTime())->modify('+' . $expiry . ' seconds'),
            $this->appPath->getWebRoot(),
            $this->getCookieDomain(),
            $this->request->getSymfonyRequest()->isSecure()
        );
    }

    /**
     * Loggt einen User ein.
     *
     * @param string $username
     * @param string $password
     * @param bool   $rememberMe
     *
     * @throws Users\Exception\LoginFailedException
     * @throws Users\Exception\UserAccountLockedException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function login($username, $password, $rememberMe)
    {
        $user = $this->userRepository->getOneByNickname($username);

        if (!empty($user)) {
            // The user account has been locked
            if ($user['login_errors'] >= 3) {
                throw new UserAccountLockedException();
            }

            if ($this->userHasOldPassword($password, $user)) {
                $user = $this->migratePasswordHashToSha512($user['id'], $password);
            }

            if ($user['pwd'] === $this->secureHelper->generateSaltedPassword($user['pwd_salt'], $password, 'sha512')) {
                if ($user['login_errors'] > 0) {
                    $this->userRepository->update(['login_errors' => 0], (int) $user['id']);
                }

                if ($rememberMe === true) {
                    $token = $this->generateRememberMeToken($user);
                    $this->saveRememberMeToken($user['id'], $token);
                }

                $this->sessionHandler->getFlashBag()->clear();
                $this->sessionHandler->migrate(true);

                $this->authenticate($user);
                $this->setSessionValues();

                return;
            }

            if ($this->saveFailedLoginAttempts($user) === 3) {
                throw new UserAccountLockedException();
            }
        }

        throw new LoginFailedException();
    }

    private function setSessionValues(): void
    {
        $this->sessionHandler->set(self::AUTH_NAME, [
            'id' => $this->userModel->getUserId(),
            'super_user' => $this->userModel->isSuperUser(),
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function saveFailedLoginAttempts(array $userData): int
    {
        $loginErrors = $userData['login_errors'] + 1;
        $this->userRepository->update(['login_errors' => $loginErrors], (int) $userData['id']);

        return $loginErrors;
    }

    protected function getCookieDomain(): string
    {
        if (strpos($this->request->getServer()->get('HTTP_HOST'), '.') !== false) {
            return $this->request->getServer()->get('HTTP_HOST', '');
        }

        return '';
    }

    protected function generateRememberMeToken(array $user): string
    {
        return hash('sha512', $user['id'] . ':' . $user['pwd_salt'] . ':' . uniqid((string) mt_rand(), true));
    }

    /**
     * @param int    $userId
     * @param string $token
     *
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function saveRememberMeToken($userId, $token)
    {
        return $this->userRepository->update(['remember_me_token' => $token], $userId);
    }

    /**
     * Migrates the old sha1 based password hash to sha512 hashes and returns the updated user information.
     *
     * @param int    $userId
     * @param string $password
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function migratePasswordHashToSha512($userId, $password): array
    {
        $salt = $this->secureHelper->salt(self::SALT_LENGTH);
        $updateValues = [
            'pwd' => $this->secureHelper->generateSaltedPassword($salt, $password, 'sha512'),
            'pwd_salt' => $salt,
        ];

        $this->userRepository->update($updateValues, $userId);

        return $this->userRepository->getOneById($userId);
    }

    /**
     * @param string $password
     */
    protected function userHasOldPassword($password, array $user): bool
    {
        return \strlen($user['pwd']) === 40
        && $user['pwd'] === $this->secureHelper->generateSaltedPassword($user['pwd_salt'], $password);
    }
}
