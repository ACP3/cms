<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Maintenance;

use ACP3\Core;
use ACP3\Core\Controller\Context;
use ACP3\Modules\ACP3\System;
use Composer\Semver\Comparator;

class UpdateCheck extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var System\Helper\UpdateCheck
     */
    private $updateCheck;

    /**
     * UpdateCheck constructor.
     * @param Context\FrontendContext $context
     * @param System\Helper\UpdateCheck $updateCheck
     */
    public function __construct(Context\FrontendContext $context, System\Helper\UpdateCheck $updateCheck)
    {
        parent::__construct($context);

        $this->updateCheck = $updateCheck;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return [
            'update' => $this->updateCheck->checkForNewVersion()
        ];
    }
}
