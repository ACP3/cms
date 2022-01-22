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
    public function setIdentifier(string $identifier): self;

    public function getPrimaryKey(): ?string;

    /**
     * @return $this
     */
    public function setPrimaryKey(?string $primaryKey): self;

    public function getUseAjax(): bool;

    /**
     * @return $this
     */
    public function setUseAjax(bool $useAjax): self;

    public function getTotalResults(): ?int;

    /**
     * @return $this
     */
    public function setTotalResults(int $totalResults): self;

    /**
     * @param array<string, mixed> $column
     * @param array<string, mixed> $dbResultRow
     *
     * @return string|array<string, mixed>
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow): string|array;
}
