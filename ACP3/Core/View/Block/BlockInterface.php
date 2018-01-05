<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Block;

use Symfony\Component\HttpFoundation\Response;

interface BlockInterface
{
    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data);

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @param string $templateName
     * @return $this
     */
    public function setTemplate(string $templateName);

    /**
     * @return array|Response
     */
    public function render();
}
