<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Contact\Model\Repository\DataGridRepository
     */
    private $dataGridRepository;
    /**
     * @var Core\View\Block\DataGridBlockInterface
     */
    private $block;

    /**
     * Index constructor.
     * @param Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\DataGridBlockInterface $block
     * @param Contact\Model\Repository\DataGridRepository $dataGridRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\DataGridBlockInterface $block,
        Contact\Model\Repository\DataGridRepository $dataGridRepository
    ) {
        parent::__construct($context);

        $this->dataGridRepository = $dataGridRepository;
        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        return $this->block
            ->setDataGridRepository($this->dataGridRepository)
            ->render();
    }
}
