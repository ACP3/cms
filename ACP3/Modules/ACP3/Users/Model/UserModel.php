<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Model;

use ACP3\Core\Helpers\Country;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
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
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    protected $config;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository
     */
    protected $userRepository;

    /**
     * UserModel constructor.
     *
     * @param \ACP3\Core\Settings\SettingsInterface $config
     * @param \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository $userRepository
     */
    public function __construct(
        SettingsInterface $config,
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
     * Returns, whether the current user is an authenticated user or not
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->isAuthenticated === true && $this->getUserId() !== 0;
    }

    /**
     * @param boolean $isAuthenticated
     * @return $this
     */
    public function setIsAuthenticated($isAuthenticated)
    {
        $this->isAuthenticated = (bool)$isAuthenticated;

        return $this;
    }

    /**
     * Returns the user id of the currently logged in user
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Returns the users default language
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
        $userSettings = $this->config->getSettings(Users\Installer\Schema::MODULE_NAME);
        $systemSettings = $this->config->getSettings(Schema::MODULE_NAME);

        $this->language = $systemSettings['lang'];
        if ($userSettings['language_override'] == 1 && !empty($language)) {
            $this->language = $language;
        }

        return $this;
    }

    /**
     * @return int
     * @deprecated Use the \ACP3\Core\Helpers\ResultsPerPage::getResultsPerPage() method instead. Will be removed in version 4.5.0
     */
    public function getEntriesPerPage()
    {
        return $this->entriesPerPage;
    }

    /**
     * @return $this
     * @deprecated will be removed in version 4.5.0
     */
    public function setEntriesPerPage()
    {
        $this->entriesPerPage = (int)$this->config->getSettings(Schema::MODULE_NAME)['entries'];

        return $this;
    }

    /**
     * Returns, whether the currently logged in user is a super user or not
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
