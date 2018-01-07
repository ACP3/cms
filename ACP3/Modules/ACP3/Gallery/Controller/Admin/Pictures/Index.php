<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;

class Index extends AbstractFrontendAction
{
    /**
     * @var Core\View\Block\DataGridBlockInterface
     */
    private $block;

    /**
     * Index constructor.
     *
     * @param Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\DataGridBlockInterface  $block
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\DataGridBlockInterface $block
    ) {
        parent::__construct($context);

        $this->block = $block;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function execute(int $id)
    {
        return $this->block
            ->setData(['gallery_id' => $id])
            ->render();
    }
}
