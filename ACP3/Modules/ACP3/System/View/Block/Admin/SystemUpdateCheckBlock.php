<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\View\Block\Admin;

use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\System\Helper\UpdateCheck;

class SystemUpdateCheckBlock extends AbstractBlock
{
    /**
     * @var UpdateCheck
     */
    private $updateCheck;

    /**
     * SystemUpdateCheckBlock constructor.
     * @param BlockContext $context
     * @param UpdateCheck $updateCheck
     */
    public function __construct(BlockContext $context, UpdateCheck $updateCheck)
    {
        parent::__construct($context);

        $this->updateCheck = $updateCheck;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        return [
            'update' => $this->updateCheck->checkForNewVersion()
        ];
    }
}
