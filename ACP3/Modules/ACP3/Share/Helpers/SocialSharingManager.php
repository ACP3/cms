<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Helpers;

use ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository;
use ACP3\Modules\ACP3\Share\Model\ShareModel;

class SocialSharingManager
{
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\ShareModel
     */
    private $shareModel;
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository
     */
    private $shareRepository;

    /**
     * SocialSharingManager constructor.
     *
     * @param \ACP3\Modules\ACP3\Share\Model\ShareModel                 $shareModel
     * @param \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository $shareRepository
     */
    public function __construct(
        ShareModel $shareModel,
        ShareRepository $shareRepository
    ) {
        $this->shareModel = $shareModel;
        $this->shareRepository = $shareRepository;
    }

    /**
     * Deletes the given sharing info.
     *
     * @param string $path
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteSharingInfo(string $path): bool
    {
        $path .= $this->preparePath($path);
        $shareInfo = $this->shareRepository->getOneByUri($path);

        return !empty($shareInfo) && $this->shareModel->delete($shareInfo['id']) !== false;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function preparePath(string $path): string
    {
        return !\preg_match('/\/$/', $path) ? '/' : '';
    }

    /**
     * Inserts/Updates the given sharing info.
     *
     * @param string $path
     * @param bool   $active
     * @param array  $services
     * @param bool   $ratingsActive
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function saveSharingInfo(
        string $path,
        bool $active = false,
        array $services = [],
        bool $ratingsActive = false): bool
    {
        $path .= $this->preparePath($path);
        $data = [
            'uri' => $path,
            'share_active' => $active,
            'share_services' => $services,
            'share_ratings_active' => $ratingsActive,
        ];

        $sharingInfo = $this->shareRepository->getOneByUri($path);

        return $this->shareModel->save($data, $sharingInfo['id'] ?? null) !== false;
    }
}
