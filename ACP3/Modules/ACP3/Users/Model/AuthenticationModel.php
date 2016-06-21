<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Model;


use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Session\SessionHandlerInterface;
use ACP3\Modules\ACP3\Users;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthenticationModel
 * @package ACP3\Modules\ACP3\Users\Model
 */
class AuthenticationModel
{
    const AUTH_NAME = 'ACP3_AUTH';
    const SALT_LENGTH = 16;
    const REMEMBER_ME_COOKIE_LIFETIME = 31104000;

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
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var Response
     */
    protected $response;
    /**
     * @var UserModel
     */
    protected $userModel;

    /**
     * User constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param Response $response
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Session\SessionHandlerInterface $sessionHandler
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param UserModel $userModel
     * @param \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository $userRepository
     */
    public function __construct(
        RequestInterface $request,
        Response $response,
        ApplicationPath $appPath,
        SessionHandlerInterface $sessionHandler,
        Secure $secureHelper,
        UserModel $userModel,
        Users\Model\Repository\UserRepository $userRepository
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->appPath = $appPath;
        $this->sessionHandler = $sessionHandler;
        $this->secureHelper = $secureHelper;
        $this->userModel = $userModel;
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticates the user
     * @param array|int|null $userData
     */
    public function authenticate($userData)
    {
        if (is_array($userData)) {
            $this->userModel
                ->setIsAuthenticated(true)
                ->setUserId($userData['id'])
                ->setIsSuperUser($userData['super_user'])
                ->setLanguage($userData['language'])
                ->setEntriesPerPage($userData['entries']);
        } else {
            $this->userModel
                ->setEntriesPerPage('')
                ->setLanguage('');
        }
    }

    /**
     * Logs out the current user
     *
     * @param int $userId
     */
    public function logout($userId = 0)
    {
        if ($userId === 0) {
            $userId = $this->userModel->getUserId();
        }

        $this->saveRememberMeToken($userId, '');
        $this->sessionHandler->destroy(session_id());
        $this->response->headers->setCookie(
            $this->setRememberMeCookie($userId, '', -1 * self::REMEMBER_ME_COOKIE_LIFETIME)
        );
    }

    /**
     * Setzt den internen Authentifizierungscookie
     *
     * @param int $userId
     * @param string $token
     * @param integer|null $expiry
     *
     * @return Cookie
     */
    public function setRememberMeCookie($userId, $token, $expiry = null)
    {
        if ($expiry === null) {
            $expiry = static::REMEMBER_ME_COOKIE_LIFETIME;
        }

        return new Cookie(
            self::AUTH_NAME,
            $userId . '|' . $token,
            (new \DateTime())->modify('+' . $expiry . ' seconds'),
            $this->appPath->getWebRoot(),
            $this->getCookieDomain()
        );
    }

    /**
     * Loggt einen User ein
     *
     * @param string $username
     * @param string $password
     * @param bool $rememberMe
     * @throws Users\Exception\LoginFailedException
     * @throws Users\Exception\UserAccountLockedException
     */
    public function login($username, $password, $rememberMe)
    {
        $user = $this->userRepository->getOneByNickname($username);

        if (!empty($user)) {
            // The user account has been locked
            if ($user['login_errors'] >= 3) {
                throw new Users\Exception\UserAccountLockedException();
            }

            if ($this->userHasOldPassword($password, $user)) {
                $user = $this->migratePasswordHashToSha512($user['id'], $password);
            }

            if ($user['pwd'] === $this->secureHelper->generateSaltedPassword($user['pwd_salt'], $password, 'sha512')) {
                if ($user['login_errors'] > 0) {
                    $this->userRepository->update(['login_errors' => 0], (int)$user['id']);
                }

                if ($rememberMe === true) {
                    $token = $this->generateRememberMeToken($user);
                    $this->saveRememberMeToken($user['id'], $token);
                    $this->response->headers->setCookie(
                        $this->setRememberMeCookie($user['id'], $token)
                    );
                }

                $this->sessionHandler->secureSession();

                $this->authenticate($user);
                $this->setSessionValues();
                return;
            } elseif ($this->saveFailedLoginAttempts($user) === 3) {
                throw new Users\Exception\UserAccountLockedException();
            }
        }

        throw new Users\Exception\LoginFailedException();
    }

    private function setSessionValues()
    {
        $this->sessionHandler->set(self::AUTH_NAME, [
            'id' => $this->userModel->getUserId(),
            'super_user' => $this->userModel->isSuperUser(),
            'entries' => $this->userModel->getEntriesPerPage(),
            'language' => $this->userModel->getLanguage()
        ]);
    }

    /**
     * @param array $userData
     *
     * @return int
     */
    protected function saveFailedLoginAttempts(array $userData)
    {
        $loginErrors = $userData['login_errors'] + 1;
        $this->userRepository->update(['login_errors' => $loginErrors], (int)$userData['id']);
        return $loginErrors;
    }

    /**
     * @return string
     */
    protected function getCookieDomain()
    {
        if (strpos($this->request->getServer()->get('HTTP_HOST'), '.') !== false) {
            return $this->request->getServer()->get('HTTP_HOST', '');
        }
        return '';
    }

    /**
     * @param array $user
     *
     * @return string
     */
    protected function generateRememberMeToken(array $user)
    {
        return hash('sha512', $user['id'] . ':' . $user['pwd_salt'] . ':' . uniqid(mt_rand()));
    }

    /**
     * @param int $userId
     * @param string $token
     *
     * @return bool|int
     */
    private function saveRememberMeToken($userId, $token)
    {
        return $this->userRepository->update(['remember_me_token' => $token], $userId);
    }

    /**
     * Migrates the old sha1 based password hash to sha512 hashes and returns the updated user information
     *
     * @param int $userId
     * @param string $password
     *
     * @return bool|int
     */
    private function migratePasswordHashToSha512($userId, $password)
    {
        $salt = $this->secureHelper->salt(self::SALT_LENGTH);
        $updateValues = [
            'pwd' => $this->secureHelper->generateSaltedPassword($salt, $password, 'sha512'),
            'pwd_salt' => $salt
        ];

        $this->userRepository->update($updateValues, $userId);

        return $this->userRepository->getOneById($userId);
    }

    /**
     * @param string $password
     * @param array $user
     *
     * @return bool
     */
    protected function userHasOldPassword($password, array $user)
    {
        return strlen($user['pwd']) === 40
        && $user['pwd'] === $this->secureHelper->generateSaltedPassword($user['pwd_salt'], $password);
    }
}
