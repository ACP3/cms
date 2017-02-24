<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Model;

use ACP3\Core\Helpers\Country;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Users;

class UserModel
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
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository
     */
    protected $userRepository;
    /**
     * @var Translator
     */
    private $translator;

    /**
     * UserModel constructor.
     *
     * @param Translator $translator
     * @param \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository $userRepository
     */
    public function __construct(
        Translator $translator,
        Users\Model\Repository\UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->translator = $translator;
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
            $countries = Country::worldCountries($this->translator->getLocale());
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
