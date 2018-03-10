<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Helpers;

use ACP3\Core\Helpers\Forms;
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository;

class ShareFormFields
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository
     */
    private $shareRepository;

    /**
     * SharingInfoFormFields constructor.
     *
     * @param \ACP3\Core\Helpers\Forms                                  $formsHelper
     * @param \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository $shareRepository
     */
    public function __construct(Forms $formsHelper, ShareRepository $shareRepository)
    {
        $this->formsHelper = $formsHelper;
        $this->shareRepository = $shareRepository;
    }

    /**
     * Returns the sharing form fields.
     *
     * @param string $path
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function formFields(string $path = ''): array
    {
        $sharingInfo = [
            'active' => 0,
        ];

        if (!empty($path)) {
            $path .= !\preg_match('/\/$/', $path) ? '/' : '';

            $sharingInfo = $this->shareRepository->getOneByUri($path);
        }

        return [
            'active' => $this->formsHelper->yesNoCheckboxGenerator(
                'share_active',
                $sharingInfo['active']
            ),
        ];
    }
}
