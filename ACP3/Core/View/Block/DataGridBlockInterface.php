<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Block;

use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;

interface DataGridBlockInterface extends BlockInterface
{
    /**
     * @return string
     */
    public function getModuleName(): string;

    /**
     * @param AbstractDataGridRepository $dataGridRepository
     * @return $this
     */
    public function setDataGridRepository(AbstractDataGridRepository $dataGridRepository);

    /**
     * @return AbstractDataGridRepository|null
     */
    public function getDataGridRepository();
}
