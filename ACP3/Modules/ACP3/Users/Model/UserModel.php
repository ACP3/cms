<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Model;

use ACP3\Core\Config;
use ACP3\Core\Helpers\Country;
use ACP3\Modules\ACP3\Users;

/**
 * Class UserModel
 * @package ACP3\Modules\ACP3\Users\Model
 */
class UserModel
{
    const SALT_LENGTH = 16;

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
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository
     */
    protected $userRepository;

    /**
     * User constructor.
     *
     * @param \ACP3\Core\Config $config
     * @param \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository $userRepository
     */
    public function __construct(
        Config $config,
        Users\Model\Repository\UserRepository $userRepository
    ) {
        $this->config = $config;
        $this->userRepository = $userRepository;
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
     * @param $isAuthenticated
     * @return $this
     */
    public function setIsAuthenticated($isAuthenticated)
    {
        $this->isAuthenticated = (bool)$isAuthenticated;

        return $this;
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
     * @param $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
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
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $userSettings = $this->config->getSettings('users');
        $systemSettings = $this->config->getSettings('system');

        $this->language = $systemSettings['lang'];
        if ($userSettings['language_override'] == 1 && !empty($language)) {
            $this->language = $language;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getEntriesPerPage()
    {
        return $this->entriesPerPage;
    }

    /**
     * @param int $entries
     *
     * @return $this
     */
    public function setEntriesPerPage($entries)
    {
        $userSettings = $this->config->getSettings('users');
        $systemSettings = $this->config->getSettings('system');

        $this->entriesPerPage = (int)$systemSettings['entries'];
        if ($userSettings['entries_override'] == 1 && $entries > 0) {
            $this->entriesPerPage = (int)$entries;
        }

        return $this;
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
     * @param bool $isSuperUser
     * @return $this
     */
    public function setIsSuperUser($isSuperUser)
    {
        $this->superUser = (bool)$isSuperUser;

        return $this;
    }
}
