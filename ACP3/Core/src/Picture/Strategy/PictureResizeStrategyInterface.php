<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Picture\Strategy;

use ACP3\Core\Picture\Input;
use ACP3\Core\Picture\Output;

interface PictureResizeStrategyInterface
{
    /**
     * @return int this method will return the value of one of the IMAGETYPE_* constants
     */
    public function supportedImageType(): int;

    public function resize(Input $input, Output $output): void;
}
