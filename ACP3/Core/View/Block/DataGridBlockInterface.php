<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block;


use ACP3\Core\Model\Repository\DataGridRepository;

interface DataGridBlockInterface extends BlockInterface
{
    /**
     * @return string
     */
    public function getModuleName(): string;

    /**
     * @param DataGridRepository $dataGridRepository
     * @return $this
     */
    public function setDataGridRepository(DataGridRepository $dataGridRepository);

    /**
     * @return DataGridRepository|null
     */
    public function getDataGridRepository();
}
