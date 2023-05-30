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
use ACP3\Modules\ACP3\Users\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthenticationModel implements AuthenticationModelInterface
{
    private const SALT_LENGTH = 16;
    private const REMEMBER_ME_COOKIE_LIFETIME = 31_104_000;

    public function __construct(private readonly RequestInterface $request, private readonly ApplicationPath $appPath, private readonly Session $sessionHandler, private readonly Secure $secureHelper, private readonly UserModelInterface $userModel, private readonly UserRepository $userRepository)
    {
    }

    /**
     * Authenticates the user.
     *
     * @param array<string, mixed>|null $userData
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
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function logout(int $userId = 0): void
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
     * @throws \Exception
     */
    public function setRememberMeCookie(int $userId, string $token, int $expiry = null): Cookie
    {
        if ($expiry === null) {
            $expiry = self::REMEMBER_ME_COOKIE_LIFETIME;
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
     * @throws Users\Exception\LoginFailedException
     * @throws Users\Exception\UserAccountLockedException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function login(string $username, string $password, bool $rememberMe): void
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
     * @param array<string, mixed> $userData
     *
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
        if (str_contains((string) $this->request->getServer()->get('HTTP_HOST'), '.')) {
            return $this->request->getServer()->get('HTTP_HOST', '');
        }

        return '';
    }

    /**
     * @param array<string, mixed> $user
     */
    protected function generateRememberMeToken(array $user): string
    {
        return hash('sha512', $user['id'] . ':' . $user['pwd_salt'] . ':' . uniqid((string) mt_rand(), true));
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function saveRememberMeToken(int $userId, string $token): void
    {
        $this->userRepository->update(['remember_me_token' => $token], $userId);
    }

    /**
     * Migrates the old sha1 based password hash to sha512 hashes and returns the updated user information.
     *
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function migratePasswordHashToSha512(int $userId, string $password): array
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
     * @param array<string, mixed> $user
     */
    protected function userHasOldPassword(string $password, array $user): bool
    {
        return \strlen((string) $user['pwd']) === 40
        && $user['pwd'] === $this->secureHelper->generateSaltedPassword($user['pwd_salt'], $password);
    }
}
