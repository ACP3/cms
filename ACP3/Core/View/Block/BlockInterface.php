<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
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
     * @return array|Response
     */
    public function render();
}
