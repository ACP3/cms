<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Picture\Strategy;

use ACP3\Core\Picture\Input;
use ACP3\Core\Picture\Output;

class PngPictureResizeStrategy extends AbstractPictureResizeStrategy
{
    public function supportedImageType(): int
    {
        return IMAGETYPE_PNG;
    }

    public function resize(Input $input, Output $output): void
    {
        $destPicture = imagecreatetruecolor($output->getDestWidth(), $output->getDestHeight());

        imagealphablending($destPicture, false);
        imagesavealpha($destPicture, true);

        $this->doResize($output, imagecreatefrompng($input->getFile()), $destPicture);
        imagepng($destPicture, $input->getCacheFileName(), 9, PNG_ALL_FILTERS);
    }
}
