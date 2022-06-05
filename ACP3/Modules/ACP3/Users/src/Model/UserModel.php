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

    private ?bool $isAuthenticated = null;

    private int $userId = 0;

    private bool $isSuperUser = false;
    /**
     * @var array<string, mixed>[]
     */
    private array $userInfo = [];

    public function __construct(private readonly CountryList $countryList, protected Users\Repository\UserRepository $userRepository)
    {
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated === true && $this->getUserId() !== 0;
    }

    /**
     * {@inheritDoc}
     */
    public function setIsAuthenticated(bool $isAuthenticated): self
    {
        $this->isAuthenticated = $isAuthenticated;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * {@inheritDoc}
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isSuperUser(): bool
    {
        return $this->isSuperUser;
    }

    /**
     * {@inheritDoc}
     */
    public function setIsSuperUser(bool $isSuperUser): self
    {
        $this->isSuperUser = $isSuperUser;

        return $this;
    }
}
