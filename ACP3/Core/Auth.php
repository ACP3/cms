<?php
namespace ACP3\Core;

use ACP3\Core\Helpers\Country;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Users;

/**
 * Class Auth
 * @package ACP3\Core
 */
class Auth
{
    /**
     * Name des Authentifizierungscookies
     */
    const AUTH_NAME = 'ACP3_AUTH';
    /**
     * Anzuzeigende Datensätze  pro Seite
     *
     * @var integer
     */
    public $entries = '';
    /**
     * Standardsprache des Benutzers
     *
     * @var string
     */
    public $language = '';
    /**
     * Eingeloggter Benutzer oder nicht
     *
     * @var boolean
     */
    protected $isUser = false;
    /**
     * ID des Benutzers
     *
     * @var integer
     */
    protected $userId = 0;
    /**
     * Super User oder nicht
     *
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
     * @var \ACP3\Core\SessionHandler
     */
    protected $sessionHandler;
    /**
     * @var \ACP3\Core\Config
     */
    protected $usersConfig;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model
     */
    protected $usersModel;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;

    /**
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\SessionHandler        $sessionHandler
     * @param \ACP3\Core\Helpers\Secure        $secureHelper
     * @param \ACP3\Core\Config                $config
     * @param \ACP3\Modules\ACP3\Users\Model   $usersModel
     */
    public function __construct(
        RequestInterface $request,
        SessionHandler $sessionHandler,
        Secure $secureHelper,
        Config $config,
        Users\Model $usersModel
    )
    {
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
        $this->secureHelper = $secureHelper;
        $this->config = $config;
        $this->usersModel = $usersModel;
    }

    /**
     * Authenticates the user
     */
    public function authenticate()
    {
        $settings = $this->config->getSettings('system');

        $this->entries = $settings['entries'];
        $this->language = $settings['lang'];

        $credentials = [];
        if ($this->sessionHandler->has(self::AUTH_NAME)) {
            $credentials = $this->sessionHandler->get(self::AUTH_NAME, []);
        } elseif ($this->request->getCookie()->has(self::AUTH_NAME)) {
            $cookie = $this->request->getCookie()->get(self::AUTH_NAME, '');
            $credentials = explode('|', $cookie);
        }

        if (!empty($credentials) && !$this->verifyCredentials($credentials)) {
            $this->logout();
        }
    }

    /**
     * @param array $credentials
     *
     * @return bool
     */
    protected function verifyCredentials(array $credentials)
    {
        $user = $this->usersModel->getOneActiveUserByNickname($credentials[0]);
        if (!empty($user) && $this->getPasswordHash($user['pwd']) === $credentials[1]) {
            $this->successfulAuthentication($user);
            return true;
        }

        return false;
    }

    /**
     * Loggt einen User aus
     *
     * @return boolean
     */
    public function logout()
    {
        $this->sessionHandler->destroy(session_id());
        return $this->setCookie('', '', -50400);
    }

    /**
     * Setzt den internen Authentifizierungscookie
     *
     * @param string  $nickname
     *  Der Loginname des Users
     * @param string  $password
     *  Die Hashsumme des Passwortes
     * @param integer $expiry
     *  Zeit in Sekunden, bis der Cookie seine Gültigkeit verliert
     *
     * @return bool
     */
    public function setCookie($nickname, $password, $expiry)
    {
        $value = $nickname . '|' . $password;
        $expiry = time() + $expiry;
        return setcookie(self::AUTH_NAME, $value, $expiry, ROOT_DIR, $this->getCookieDomain());
    }

    /**
     * Gibt ein Array mit den angeforderten Daten eines Benutzers zurück
     *
     * @param int $userId
     *    Der angeforderte Benutzer
     *
     * @return array
     */
    public function getUserInfo($userId = 0)
    {
        if (empty($userId) && $this->isUser() === true) {
            $userId = $this->getUserId();
        }

        $userId = (int)$userId;

        if (empty($this->userInfo[$userId])) {
            $countries = Country::worldCountries();
            $info = $this->usersModel->getOneById($userId);
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
    public function isUser()
    {
        return $this->isUser === true && $this->getUserId() !== 0;
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
    public function getUserLanguage()
    {
        return $this->language;
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
        $user = $this->usersModel->getOneByNickname($username);

        if (!empty($user)) {
            // Useraccount ist gesperrt
            if ($user['login_errors'] >= 3) {
                return -1;
            }

            // The hashed password (without the salt) from the database
            $dbHash = $this->getPasswordHash($user['pwd']);

            // Get the salt of the password
            $salt = $this->getPasswordSalt($user['pwd']);
            // Generate the hash for the typed in password
            $formPasswordHash = $this->secureHelper->generateSaltedPassword($salt, $password);

            if ($dbHash === $formPasswordHash) {
                if ($user['login_errors'] > 0) {
                    $this->usersModel->update(['login_errors' => 0], (int)$user['id']);
                }

                if ($rememberMe === true) {
                    $this->setCookie($username, $dbHash, 31104000);
                }

                $this->sessionHandler->secureSession();

                $this->setSessionValues($username, $dbHash);
                $this->successfulAuthentication($user);

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
     * @param string $username
     * @param string $dbHash
     */
    private function setSessionValues($username, $dbHash)
    {
        $this->sessionHandler->set(self::AUTH_NAME, [
            $username,
            $dbHash
        ]);
    }

    /**
     * @param array $user
     *
     * @return int
     */
    protected function saveFailedLoginAttempts(array $user)
    {
        $loginErrors = $user['login_errors'] + 1;
        $this->usersModel->update(['login_errors' => $loginErrors], (int)$user['id']);
        return $loginErrors;
    }

    /**
     * @param array $user
     */
    protected function successfulAuthentication(array $user)
    {
        $this->isUser = true;
        $this->userId = (int)$user['id'];
        $this->superUser = (bool)$user['super_user'];

        $settings = $this->config->getSettings('users');

        if ($settings['entries_override'] == 1 && $user['entries'] > 0) {
            $this->entries = (int)$user['entries'];
        }
        if ($settings['language_override'] == 1) {
            $this->language = $user['language'];
        }
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
     * @param string $passwordAndSalt
     *
     * @return string
     */
    protected function getPasswordHash($passwordAndSalt)
    {
        return substr($passwordAndSalt, 0, 40);
    }

    /**
     * @param string $passwordAndSalt
     *
     * @return string
     */
    protected function getPasswordSalt($passwordAndSalt)
    {
        return substr($passwordAndSalt, 41, 53);
    }
}
