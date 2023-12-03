<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Factory;

use RFM\Repository\ItemModelInterface;
use RFM\Repository\Local\ItemModel as LocalItemModel;

class Factory
{
    /**
     * Return new item instance for image thumbnail.
     */
    public function createThumbnailModel(ItemModelInterface $imageModel): ItemModelInterface
    {
        $storage = $imageModel->getStorage();
        $path = $imageModel->getThumbnailPath();

        if ($storage->config('images.thumbnail.useLocalStorage') === true) {
            return new LocalItemModel($path, true);
        }
        $itemClass = \get_class($imageModel);

        return new $itemClass($path, true);
    }
}
