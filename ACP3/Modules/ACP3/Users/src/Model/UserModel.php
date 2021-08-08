<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Model;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\I18n\CountryList;
use ACP3\Modules\ACP3\Users;

class UserModel implements UserModelInterface
{
    public const SALT_LENGTH = 16;

    /**
     * @var bool|null
     */
    protected $isAuthenticated;
    /**
     * @var int
     */
    protected $userId = 0;
    /**
     * @var bool
     */
    protected $isSuperUser = false;
    /**
     * @var array
     */
    protected $userInfo = [];
    /**
     * @var \ACP3\Modules\ACP3\Users\Repository\UserRepository
     */
    protected $userRepository;
    /**
     * @var CountryList
     */
    private $countryList;

    public function __construct(
        CountryList $countryList,
        Users\Repository\UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->countryList = $countryList;
    }

    /**
     * Gibt ein Array mit den angeforderten Daten eines Benutzers zurück.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getUserInfo(int $userId = 0): array
    {
        if (empty($userId) && $this->isAuthenticated() === true) {
            $userId = $this->getUserId();
        }

        if (empty($this->userInfo[$userId])) {
            $countries = $this->countryList->worldCountries();
            $info = $this->userRepository->getOneById($userId);
            if (!empty($info)) {
                $info['country_formatted'] = $countries[$info['country']] ?? '';
                $this->userInfo[$userId] = $info;
            }
        }

        return $this->userInfo[$userId] ?? [];
    }

    /**
     * Returns, whether the current user is an authenticated user or not.
     */
    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated === true && $this->getUserId() !== 0;
    }

    /**
     * @return $this
     */
    public function setIsAuthenticated(bool $isAuthenticated)
    {
        $this->isAuthenticated = $isAuthenticated;

        return $this;
    }

    /**
     * Returns the user id of the currently logged in user.
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return $this
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Returns, whether the currently logged in user is a super user or not.
     */
    public function isSuperUser(): bool
    {
        return $this->isSuperUser;
    }

    /**
     * @return $this
     */
    public function setIsSuperUser(bool $isSuperUser)
    {
        $this->isSuperUser = $isSuperUser;

        return $this;
    }
}
