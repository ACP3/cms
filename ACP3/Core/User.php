<?php
namespace ACP3\Core;

use ACP3\Core\Authentication\AuthenticationInterface;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\Country;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Users;

/**
 * Class User
 * @package ACP3\Core
 */
class User
{
    const AUTH_NAME = 'ACP3_AUTH';
    const SALT_LENGTH = 16;
    const REMEMBER_ME_COOKIE_LIFETIME = 31104000;

    /**
     * @var integer
     */
    protected $entriesPerPage = 0;
    /**
     * @var string
     */
    protected $language = '';
    /**
     * @var boolean
     */
    protected $isAuthenticated = false;
    /**
     * @var integer
     */
    protected $userId = 0;
    /**
     * @var boolean
     */
    protected $superUser = false;
    /**
     * @var array
     */
    protected $userInfo = [];
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Authentication\AuthenticationInterface
     */
    protected $authentication;
    /**
     * @var \ACP3\Core\SessionHandler
     */
    protected $sessionHandler;
    /**
     * @var \ACP3\Core\Config
     */
    protected $usersConfig;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
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
     * @param \ACP3\Core\Http\RequestInterface                  $request
     * @param \ACP3\Core\Environment\ApplicationPath            $appPath
     * @param \ACP3\Core\Authentication\AuthenticationInterface $authentication
     * @param \ACP3\Core\SessionHandler                         $sessionHandler
     * @param \ACP3\Core\Helpers\Secure                         $secureHelper
     * @param \ACP3\Core\Config                                 $config
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository     $userRepository
     */
    public function __construct(
        RequestInterface $request,
        ApplicationPath $appPath,
        AuthenticationInterface $authentication,
        SessionHandler $sessionHandler,
        Secure $secureHelper,
        Config $config,
        Users\Model\UserRepository $userRepository
    )
    {
        $this->request = $request;
        $this->appPath = $appPath;
        $this->authentication = $authentication;
        $this->sessionHandler = $sessionHandler;
        $this->secureHelper = $secureHelper;
        $this->config = $config;
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticates the user
     */
    public function authenticate()
    {
        $userData = $this->authentication->authenticate();

        if (is_array($userData)) {
            $this->populateUserData($userData);
        } else if ($userData === 0) {
            $settings = $this->config->getSettings('system');

            $this->entriesPerPage = $settings['entries'];
            $this->language = $settings['lang'];
        } else {
            $this->logout();
        }
    }

    /**
     * Logs out the current user
     *
     * @param int $userId
     */
    public function logout($userId = 0)
    {
        $this->saveRememberMeToken($userId, '');
        $this->sessionHandler->destroy(session_id());
        $this->setRememberMeCookie(0, '', -1 * self::REMEMBER_ME_COOKIE_LIFETIME);
    }

    /**
     * Setzt den internen Authentifizierungscookie
     *
     * @param int     $userId
     * @param string  $token
     * @param integer $expiry
     *
     * @return bool
     */
    public function setRememberMeCookie($userId, $token, $expiry)
    {
        $this->request->getCookies()->set(
            self::AUTH_NAME,
            $userId . '|' . $token,
            time() + $expiry,
            $this->appPath->getWebRoot(),
            $this->getCookieDomain()
        );
    }

    /**
     * Gibt ein Array mit den angeforderten Daten eines Benutzers zurück
     *
     * @param int $userId
     *
     * @return array
     */
    public function getUserInfo($userId = 0)
    {
        if (empty($userId) && $this->isAuthenticated() === true) {
            $userId = $this->getUserId();
        }

        $userId = (int)$userId;

        if (empty($this->userInfo[$userId])) {
            $countries = Country::worldCountries();
            $info = $this->userRepository->getOneById($userId);
            if (!empty($info)) {
                $info['country_formatted'] = !empty($info['country']) && isset($countries[$info['country']]) ? $countries[$info['country']] : '';
                $this->userInfo[$userId] = $info;
            }
        }

        return !empty($this->userInfo[$userId]) ? $this->userInfo[$userId] : [];
    }

    /**
     * Gibt den Status von $isUser zurück
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->isAuthenticated === true && $this->getUserId() !== 0;
    }

    /**
     * Gibt die UserId des eingeloggten Benutzers zurück
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Gibt die eingestellte Standardsprache des Benutzers aus
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return int
     */
    public function getEntriesPerPage()
    {
        return $this->entriesPerPage;
    }

    /**
     * Gibt aus, ob der aktuell eingeloggte Benutzer der Super User ist, oder nicht
     *
     * @return boolean
     */
    public function isSuperUser()
    {
        return $this->superUser;
    }

    /**
     * Loggt einen User ein
     *
     * @param string $username
     * @param string $password
     * @param bool   $rememberMe
     *
     * @return integer
     */
    public function login($username, $password, $rememberMe)
    {
        $user = $this->userRepository->getOneByNickname($username);

        if (!empty($user)) {
            // The user account has been locked
            if ($user['login_errors'] >= 3) {
                return -1;
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
                    $this->setRememberMeCookie($user['id'], $token, self::REMEMBER_ME_COOKIE_LIFETIME);
                }

                $this->sessionHandler->secureSession();

                $this->setSessionValues($user);

                return 1;
            } else {
                if ($this->saveFailedLoginAttempts($user) === 3) {
                    return -1;
                }
            }
        }
        return 0;
    }

    /**
     * @param array $userData
     */
    private function setSessionValues(array $userData)
    {
        $this->sessionHandler->set(self::AUTH_NAME, [
            'id' => $userData['id'],
            'super_user' => (bool)$userData['super_user'],
            'entries' => $this->setEntriesPerPage($userData),
            'language' => $this->setLanguage($userData)
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
     * @param array $data
     */
    protected function populateUserData(array $data)
    {
        $this->isAuthenticated = true;
        $this->userId = (int)$data['id'];
        $this->superUser = (bool)$data['super_user'];
        $this->entriesPerPage = (int)$data['entries'];
        $this->language = $data['language'];
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
     * @return int
     */
    private function setEntriesPerPage(array $user)
    {
        $userSettings = $this->config->getSettings('users');
        $systemSettings = $this->config->getSettings('system');

        if ($userSettings['entries_override'] == 1 && $user['entries'] > 0) {
            return (int)$user['entries'];
        }

        return (int)$systemSettings['entries'];
    }

    /**
     * @param array $user
     *
     * @return string
     */
    private function setLanguage(array $user)
    {
        $userSettings = $this->config->getSettings('users');
        $systemSettings = $this->config->getSettings('system');

        if ($userSettings['language_override'] == 1) {
            return $user['language'];
        }

        return $systemSettings['lang'];
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
     * @param int    $userId
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
     * @param int    $userId
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
     * @param array  $user
     *
     * @return bool
     */
    protected function userHasOldPassword($password, array $user)
    {
        return strlen($user['pwd']) === 40 && $user['pwd'] === $this->secureHelper->generateSaltedPassword($user['pwd_salt'], $password);
    }
}
