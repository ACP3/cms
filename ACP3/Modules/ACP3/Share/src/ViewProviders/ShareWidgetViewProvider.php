<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\ViewProviders;

use ACP3\Modules\ACP3\Share\Repository\ShareRatingsRepository;
use ACP3\Modules\ACP3\Share\Repository\ShareRepository;

class ShareWidgetViewProvider
{
    public function __construct(
        private readonly ShareRepository $shareRepository,
        private readonly ShareRatingsRepository $shareRatingsRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(string $path): array
    {
        $path = urldecode($path);

        $sharingInfo = $this->shareRepository->getOneByUri($path);

        if (empty($sharingInfo)) {
            return [];
        }

        $sharing = [];
        $sharing['active'] = ((int) $sharingInfo['active']) === 1;
        $sharing['ratings_active'] = ((int) $sharingInfo['ratings_active']) === 1;
        $sharing['rating'] = $this->shareRatingsRepository->getRatingStatistics($sharingInfo['id']);
        $sharing['rating']['share_id'] = $sharingInfo['id'];

        if (((int) $sharingInfo['active']) === 1) {
            $sharing['path'] = $path;
        }

        return [
            'sharing' => $sharing,
        ];
    }
}
