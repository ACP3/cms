<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Resources;

use ACP3\Core;

class Asset extends AbstractResource
{
    /**
     * @var \ACP3\Core\Assets\FileResolver
     */
    protected $fileResolver;

    public function __construct(Core\Assets\FileResolver $fileResolver)
    {
        $this->fileResolver = $fileResolver;
    }

    /**
     * fetch template and its modification time from data source.
     *
     * @param string $name   template name
     * @param string $source template source
     * @param int    $mtime  template modification timestamp (epoch)
     */
    protected function fetch($name, &$source, &$mtime)
    {
        $asset = $this->fileResolver->resolveTemplatePath($name);

        if ($asset !== '') {
            $source = file_get_contents($asset);
            $mtime = filemtime($asset);
        } else {
            $source = null;
            $mtime = null;
        }
    }

    /**
     * Fetch a template's modification time from data source.
     *
     * @param string $name template name
     *
     * @return int timestamp (epoch) the template was modified
     */
    protected function fetchTimestamp($name)
    {
        $asset = $this->fileResolver->resolveTemplatePath($name);

        return filemtime($asset);
    }
}
