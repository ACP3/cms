<?php
namespace ACP3\Installer\Core\View\Renderer\Smarty\Resources;

use ACP3\Core\View\Renderer\Smarty\Resources\AbstractResource;
use ACP3\Installer\Core\Environment\ApplicationPath;

/**
 * Class Asset
 * @package ACP3\Installer\Core\View\Renderer\Smarty\Resources
 */
class Asset extends AbstractResource
{
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * @return string
     */
    public function getExtensionName()
    {
        return 'asset';
    }

    /**
     * Asset constructor.
     *
     * @param \ACP3\Installer\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
        $this->recompiled = true;
        $this->hasCompiledHandler = true;
    }

    /**
     * fetch template and its modification time from data source
     *
     * @param string $name template name
     * @param string &$source template source
     * @param integer &$mtime template modification timestamp (epoch)
     */
    protected function fetch($name, &$source, &$mtime)
    {
        $asset = $this->resolveTemplatePath($name);

        if ($asset !== '') {
            $source = file_get_contents($asset);
            $mtime = filemtime($asset);
        } else {
            $source = null;
            $mtime = null;
        }
    }

    /**
     * @param string $template
     *
     * @return string
     */
    protected function resolveTemplatePath($template)
    {
        // If an template with directory is given, uppercase the first letter
        if (strpos($template, '/') !== false) {
            $template = ucfirst($template);

            // Pfad zerlegen
            $fragments = explode('/', $template);

            if (count($fragments) === 3) {
                $path = $fragments[0] . '/Resources/View/' . $fragments[1] . '/' . $fragments[2];
            } else {
                $path = $fragments[0] . '/Resources/View/' . $fragments[1];
            }

            return $this->appPath->getInstallerModulesDir() . $path;
        }

        return $this->appPath->getDesignPathInternal() . $template;
    }

    /**
     * compile template from source
     *
     * @param \Smarty_Internal_Template $_smarty_tpl do not change variable name, is used by compiled template
     *
     * @throws \Exception
     */
    public function process(\Smarty_Internal_Template $_smarty_tpl)
    {
        $compiled = &$_smarty_tpl->compiled;
        $compiled->file_dependency = array();
        $compiled->includes = array();
        $compiled->nocache_hash = null;
        $compiled->unifunc = null;
        $level = ob_get_level();
        ob_start();
        $_smarty_tpl->loadCompiler();
        // call compiler
        try {
            eval("?>" . $_smarty_tpl->compiler->compileTemplate($_smarty_tpl));
        } catch (\Exception $e) {
            unset($_smarty_tpl->compiler);
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }
        // release compiler object to free memory
        unset($_smarty_tpl->compiler);
        ob_get_clean();
        $compiled->timestamp = time();
        $compiled->exists = true;
    }

    /**
     * populate Compiled Object with compiled filepath
     *
     * @param \Smarty_Template_Compiled $compiled compiled object
     * @param \Smarty_Internal_Template $_template template object
     *
     * @return void
     */
    public function populateCompiledFilepath(\Smarty_Template_Compiled $compiled, \Smarty_Internal_Template $_template)
    {
        $compiled->filepath = false;
        $compiled->timestamp = false;
        $compiled->exists = false;
    }

    /*
       * Disable timestamp checks for recompiled resource.
       *
       * @return bool
       */
    public function checkTimestamps()
    {
        return false;
    }
}
