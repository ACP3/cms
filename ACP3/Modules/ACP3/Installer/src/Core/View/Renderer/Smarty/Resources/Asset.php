<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\View\Renderer\Smarty\Resources;

use ACP3\Core\Assets\FileResolver;
use ACP3\Core\View\Renderer\Smarty\Resources\AbstractResource;

class Asset extends AbstractResource
{
    public function __construct(private readonly FileResolver $fileResolver)
    {
        $this->recompiled = true;
        $this->hasCompiledHandler = true;
    }

    /**
     * fetch template and its modification time from data source.
     *
     * @param string $name   template name
     * @param string $source template source
     * @param int    $mtime  template modification timestamp (epoch)
     */
    protected function fetch($name, &$source, &$mtime): void
    {
        $asset = $this->fileResolver->resolveTemplatePath($name);

        if ($asset !== '') {
            $source = \file_get_contents($asset);
            $mtime = \filemtime($asset);
        } else {
            $source = null;
            $mtime = null;
        }
    }

    /**
     * compile template from source.
     *
     * @param \Smarty_Internal_Template $_smarty_tpl do not change variable name, is used by compiled template
     *
     * @throws \Exception
     */
    public function process(\Smarty_Internal_Template $_smarty_tpl): void
    {
        $compiled = &$_smarty_tpl->compiled;
        $compiled->file_dependency = [];
        $compiled->includes = [];
        $compiled->nocache_hash = null;
        $compiled->unifunc = '';
        $level = \ob_get_level();
        \ob_start();
        $_smarty_tpl->loadCompiler();
        // call compiler
        try {
            eval('?>' . $_smarty_tpl->compiler->compileTemplate($_smarty_tpl));
        } catch (\Exception $e) {
            unset($_smarty_tpl->compiler);
            while (\ob_get_level() > $level) {
                \ob_end_clean();
            }

            throw $e;
        }
        // release compiler object to free memory
        unset($_smarty_tpl->compiler);
        \ob_get_clean();
        $compiled->timestamp = \time();
        $compiled->exists = true;
    }

    /**
     * populate Compiled Object with compiled filepath.
     *
     * @param \Smarty_Template_Compiled $compiled  compiled object
     * @param \Smarty_Internal_Template $_template template object
     */
    public function populateCompiledFilepath(\Smarty_Template_Compiled $compiled, \Smarty_Internal_Template $_template): void
    {
        /* @phpstan-ignore-next-line */
        $compiled->filepath = false;
        $compiled->timestamp = false;
        $compiled->exists = false;
    }

    /**
     * Disable timestamp checks for recompiled resource.
     */
    public function checkTimestamps(): bool
    {
        return false;
    }
}
