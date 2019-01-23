<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

interface ColumnRendererInterface
{
    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @param string $identifier
     *
     * @return $this
     */
    public function setIdentifier(string $identifier);

    /**
     * @return string|null
     */
    public function getPrimaryKey(): ?string;

    /**
     * @param string|null $primaryKey
     *
     * @return $this
     */
    public function setPrimaryKey(?string $primaryKey);

    /**
     * @return bool
     */
    public function getUseAjax(): bool;

    /**
     * @param bool $useAjax
     *
     * @return $this
     */
    public function setUseAjax(bool $useAjax);

    /**
     * @return int|null
     */
    public function getTotalResults(): ?int;

    /**
     * @param int $totalResults
     *
     * @return $this
     */
    public function setTotalResults(int $totalResults);

    /**
     * @param array $column
     * @param array $dbResultRow
     *
     * @return string
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow);
}
