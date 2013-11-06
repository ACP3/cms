<?php
namespace ACP3\Core;

/**
 * Authenticates the user
 *
 * @author Tino Goratsch
 */
class Auth
{
    /**
     * Name des Authentifizierungscookies
     */
    const COOKIE_NAME = 'ACP3_AUTH';
    /**
     * Eingeloggter Benutzer oder nicht
     *
     * @var boolean
     */
    private $isUser = false;
    /**
     * ID des Benutzers
     *
     * @var integer
     */
    private $userId = 0;
    /**
     * Super User oder nicht
     *
     * @var boolean
     */
    private $superUser = false;
    /**
     * Anzuzeigende Datensätze  pro Seite
     *
     * @var integer
     */
    public $entries = CONFIG_ENTRIES;
    /**
     * Standardsprache des Benutzers
     *
     * @var string
     */
    public $language = CONFIG_LANG;

    /**
     * Findet heraus, falls der ACP3_AUTH Cookie gesetzt ist, ob der
     * Seitenbesucher auch wirklich ein registrierter Benutzer des ACP3 ist
     */
    function __construct()
    {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            $cookie = base64_decode($_COOKIE[self::COOKIE_NAME]);
            $cookie_arr = explode('|', $cookie);

            $user = Registry::get('Db')->executeQuery('SELECT id, super_user, pwd, entries, language FROM ' . DB_PRE . 'users WHERE nickname = ? AND login_errors < 3', array($cookie_arr[0]))->fetchAll();
            if (count($user) === 1) {
                $db_password = substr($user[0]['pwd'], 0, 40);
                if ($db_password === $cookie_arr[1]) {
                    $this->isUser = true;
                    $this->userId = (int)$user[0]['id'];
                    $this->superUser = (bool)$user[0]['super_user'];
                    $settings = Config::getSettings('users');
                    $this->entries = $settings['entries_override'] == 1 && $user[0]['entries'] > 0 ? (int)$user[0]['entries'] : (int)CONFIG_ENTRIES;
                    $this->language = $settings['language_override'] == 1 ? $user[0]['language'] : CONFIG_LANG;
                }
            } else {
                $this->logout();
            }
        }
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
     * Gibt ein Array mit den angeforderten Daten eines Benutzers zurück
     *
     * @param integer $user_id
     *    Der angeforderte Benutzer
     * @return mixed
     */
    public function getUserInfo($user_id = '')
    {
        if (empty($user_id) && $this->isUser() === true)
            $user_id = $this->getUserId();

        if (Validate::isNumber($user_id) === true) {
            static $user_info = array();

            if (empty($user_info[$user_id])) {
                $countries = Lang::worldCountries();
                $info = Registry::get('Db')->fetchAssoc('SELECT * FROM ' . DB_PRE . 'users WHERE id = ?', array($user_id), array(\PDO::PARAM_INT));
                if (!empty($info)) {
                    $info['country_formatted'] = !empty($info['country']) && isset($countries[$info['country']]) ? $countries[$info['country']] : '';
                    $user_info[$user_id] = $info;
                }
            }

            return !empty($user_info[$user_id]) ? $user_info[$user_id] : false;
        }
        return false;
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
     * Gibt den Status von $isUser zurück
     *
     * @return boolean
     */
    public function isUser()
    {
        return $this->isUser === true && Validate::isNumber($this->getUserId()) === true ? true : false;
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
     *    Der zu verwendente Username
     * @param string $password
     *    Das zu verwendente Passwort
     * @param integer $expiry
     *    Gibt die Zeit in Sekunden an, wie lange der User eingeloggt bleiben soll
     * @return integer
     */
    public function login($username, $password, $expiry)
    {
        $user = Registry::get('Db')->fetchAssoc('SELECT id, pwd, login_errors FROM ' . DB_PRE . 'users WHERE nickname = ?', array($username));

        if (!empty($user)) {
            // Useraccount ist gesperrt
            if ($user['login_errors'] >= 3)
                return -1;

            // Passwort aus Datenbank
            $db_hash = substr($user['pwd'], 0, 40);

            // Hash für eingegebenes Passwort generieren
            $salt = substr($user['pwd'], 41, 53);
            $form_pwd_hash = Functions::generateSaltedPassword($salt, $password);

            // Wenn beide Hashwerte gleich sind, Benutzer authentifizieren
            if ($db_hash === $form_pwd_hash) {
                // Login-Fehler zurücksetzen
                if ($user['login_errors'] > 0)
                    Registry::get('Db')->update(DB_PRE . 'users', array('login_errors' => 0), array('id', (int)$user['id']));

                $this->setCookie($username, $db_hash, $expiry);

                // Neue Session-ID generieren
                Session::secureSession(true);

                $this->isUser = true;
                $this->userId = (int)$user['id'];

                return 1;
                // Beim dritten falschen Login den Account sperren
            } else {
                $login_errors = $user['login_errors'] + 1;
                Registry::get('Db')->update(DB_PRE . 'users', array('login_errors' => $login_errors), array('id' => (int)$user['id']));
                if ($login_errors === 3) {
                    return -1;
                }
            }
        }
        return 0;
    }

    /**
     * Loggt einen User aus
     *
     * @return boolean
     */
    public function logout()
    {
        Registry::get('Session')->session_destroy(session_id());
        return $this->setCookie('', '', -50400);
    }

    /**
     * Setzt den internen Authentifizierungscookie
     *
     * @param string $nickname
     *  Der Loginname des Users
     * @param string $password
     *  Die Hashsumme des Passwortes
     * @param integer $expiry
     *  Zeit in Sekunden, bis der Cookie seine Gültigkeit verliert
     */
    public function setCookie($nickname, $password, $expiry)
    {
        $value = base64_encode($nickname . '|' . $password);
        $expiry = time() + $expiry;
        $domain = strpos($_SERVER['HTTP_HOST'], '.') !== false ? $_SERVER['HTTP_HOST'] : '';
        return setcookie(self::COOKIE_NAME, $value, $expiry, ROOT_DIR, $domain);
    }
}