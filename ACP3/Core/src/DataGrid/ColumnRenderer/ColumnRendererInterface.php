<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

interface ColumnRendererInterface
{
    public function getIdentifier(): string;

    /**
     * @return $this
     */
    public function setIdentifier(string $identifier);

    public function getPrimaryKey(): ?string;

    /**
     * @return $this
     */
    public function setPrimaryKey(?string $primaryKey);

    public function getUseAjax(): bool;

    /**
     * @return $this
     */
    public function setUseAjax(bool $useAjax);

    public function getTotalResults(): ?int;

    /**
     * @return $this
     */
    public function setTotalResults(int $totalResults);

    /**
     * @return string
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow);
}
