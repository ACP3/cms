<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Picture\Strategy;

use ACP3\Core\Picture\Input;
use ACP3\Core\Picture\Output;

class JpegPictureResizeStrategy extends AbstractPictureResizeStrategy
{
    public function supportedImageType(): int
    {
        return IMAGETYPE_JPEG;
    }

    public function resize(Input $input, Output $output): void
    {
        $destPicture = imagecreatetruecolor($output->getDestWidth(), $output->getDestHeight());

        $this->doResize($output, imagecreatefromjpeg($input->getFile()), $destPicture);
        imagejpeg($destPicture, $input->getCacheFileName(), $input->getJpgQuality());
    }
}
