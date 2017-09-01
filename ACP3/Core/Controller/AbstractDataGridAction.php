<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\View\Block\DataGridBlockInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractDataGridAction extends AbstractFrontendAction
{
    /**
     * @var DataGridBlockInterface
     */
    private $block;

    /**
     * AbstractDataGridAction constructor.
     * @param Context\FrontendContext $context
     * @param DataGridBlockInterface $block
     */
    public function __construct(Context\FrontendContext $context, DataGridBlockInterface $block)
    {
        parent::__construct($context);

        $this->block = $block;
    }

    /**
     * @return array|JsonResponse
     */
    public function execute()
    {
        return $this->block->render();
    }
}
