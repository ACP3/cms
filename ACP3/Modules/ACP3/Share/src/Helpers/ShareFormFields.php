<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Helpers;

use ACP3\Core\Helpers\Forms;
use ACP3\Modules\ACP3\Share\Repository\ShareRepository;

class ShareFormFields
{
    public function __construct(private readonly Forms $formsHelper, private readonly ShareRepository $shareRepository)
    {
    }

    /**
     * Returns the sharing form fields.
     *
     * @return array<string, array<string, mixed[]>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function formFields(string $path = ''): array
    {
        $sharingInfo = $this->getData($path);

        return [
            'active' => $this->formsHelper->yesNoCheckboxGenerator(
                'share_active',
                $sharingInfo['active']
            ),
            'ratings_active' => $this->formsHelper->yesNoCheckboxGenerator(
                'share_ratings_active',
                $sharingInfo['ratings_active']
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function getData(string $path): array
    {
        $sharingInfo = [
            'active' => 0,
            'ratings_active' => 0,
        ];

        if (!empty($path)) {
            $path .= !preg_match('/\/$/', $path) ? '/' : '';

            $sharingInfo = array_merge(
                $sharingInfo,
                $this->shareRepository->getOneByUri($path)
            );
        }

        return $sharingInfo;
    }
}
