<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Model;

use ACP3\Core\I18n\CountryList;
use ACP3\Core\Model\Repository\ReaderRepositoryInterface;
use ACP3\Modules\ACP3\Users;

class UserModel implements ReaderRepositoryInterface
{
    const SALT_LENGTH = 16;

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
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UsersRepository
     */
    protected $userRepository;
    /**
     * @var CountryList
     */
    private $countryList;

    /**
     * UserModel constructor.
     *
     * @param CountryList $countryList
     * @param \ACP3\Modules\ACP3\Users\Model\Repository\UsersRepository $userRepository
     */
    public function __construct(
        CountryList $countryList,
        Users\Model\Repository\UsersRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->countryList = $countryList;
    }

    /**
     * Gibt ein Array mit den angeforderten Daten eines Benutzers zurück
     *
     * @param int $userId
     *
     * @return array
     */
    public function getOneById(int $userId)
    {
        if (empty($this->userInfo[$userId])) {
            $countries = $this->countryList->worldCountries();
            $info = $this->userRepository->getOneById($userId);
            if (!empty($info)) {
                $info['country_formatted'] = $countries[$info['country']] ?? '';
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
