<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Helpers;

use ACP3\Modules\ACP3\Share\Model\ShareModel;
use ACP3\Modules\ACP3\Share\Repository\ShareRepository;

class SocialSharingManager
{
    public function __construct(private readonly ShareModel $shareModel, private readonly ShareRepository $shareRepository)
    {
    }

    /**
     * Deletes the given sharing info.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteSharingInfo(string $path): bool
    {
        $path .= $this->preparePath($path);
        $shareInfo = $this->shareRepository->getOneByUri($path);

        return !empty($shareInfo) && $this->shareModel->delete($shareInfo['id']) !== false;
    }

    private function preparePath(string $path): string
    {
        return !preg_match('/\/$/', $path) ? '/' : '';
    }

    /**
     * Inserts/Updates the given sharing info.
     *
     * @param string[] $services
     *
     * @throws \Doctrine\DBAL\Exception
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

        return (bool) $this->shareModel->save($data, $sharingInfo['id'] ?? null);
    }
}
