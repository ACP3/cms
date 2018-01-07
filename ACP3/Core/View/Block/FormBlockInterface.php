<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Block;

interface FormBlockInterface extends BlockInterface
{
    /**
     * @return array
     */
    public function getDefaultData(): array;

    /**
     * @return array
     */
    public function getRequestData(): array;

    /**
     * @param array $requestData
     *
     * @return $this
     */
    public function setRequestData(array $requestData);
}
