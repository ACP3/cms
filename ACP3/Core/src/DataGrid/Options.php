<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid;

class Options
{
    /**
     * @var string
     */
    private $identifier = '';
    /**
     * @var int
     */
    private $recordsPerPage = 20;
    /**
     * @var bool
     */
    private $enableMassAction = true;
    /**
     * @var bool
     */
    private $enableOptions = true;
    /**
     * @var bool
     */
    private $useAjax = false;
    /**
     * @var string
     */
    private $resourcePathEdit = '';
    /**
     * @var string
     */
    private $resourcePathDelete = '';

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     *
     * @return Options
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return int
     */
    public function getRecordsPerPage(): int
    {
        return $this->recordsPerPage;
    }

    /**
     * @param int $recordsPerPage
     *
     * @return Options
     */
    public function setRecordsPerPage(int $recordsPerPage): self
    {
        $this->recordsPerPage = $recordsPerPage;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnableOptions(): bool
    {
        return $this->enableOptions;
    }

    /**
     * @param bool $enableOptions
     *
     * @return Options
     */
    public function setEnableOptions(bool $enableOptions): self
    {
        $this->enableOptions = $enableOptions;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUseAjax(): bool
    {
        return $this->useAjax;
    }

    /**
     * @param bool $useAjax
     *
     * @return Options
     */
    public function setUseAjax(bool $useAjax): self
    {
        $this->useAjax = $useAjax;

        return $this;
    }

    /**
     * @return string
     */
    public function getResourcePathEdit(): string
    {
        return $this->resourcePathEdit;
    }

    /**
     * @param string $resourcePathEdit
     *
     * @return Options
     */
    public function setResourcePathEdit(string $resourcePathEdit): self
    {
        $this->resourcePathEdit = $resourcePathEdit;

        return $this;
    }

    /**
     * @return string
     */
    public function getResourcePathDelete(): string
    {
        return $this->resourcePathDelete;
    }

    /**
     * @param string $resourcePathDelete
     *
     * @return Options
     */
    public function setResourcePathDelete(string $resourcePathDelete): self
    {
        $this->resourcePathDelete = $resourcePathDelete;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnableMassAction(): bool
    {
        return $this->enableMassAction;
    }

    /**
     * @param bool $enableMassAction
     *
     * @return Options
     */
    public function setEnableMassAction(bool $enableMassAction): self
    {
        $this->enableMassAction = $enableMassAction;

        return $this;
    }
}
