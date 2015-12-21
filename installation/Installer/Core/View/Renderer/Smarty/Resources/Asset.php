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
    }

    /**
     * @param string $template
     *
     * @return string
     */
    protected function _resolveTemplatePath($template)
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
     * fetch template and its modification time from data source
     *
     * @param string  $name    template name
     * @param string  &$source template source
     * @param integer &$mtime  template modification timestamp (epoch)
     */
    protected function fetch($name, &$source, &$mtime)
    {
        $asset = $this->_resolveTemplatePath($name);

        if ($asset !== '') {
            $source = file_get_contents($asset);
            $mtime = filemtime($asset);
        } else {
            $source = null;
            $mtime = null;
        }
    }

    /**
     * Fetch a template's modification time from data source
     *
     * @param string $name template name
     *
     * @return integer timestamp (epoch) the template was modified
     */
    protected function fetchTimestamp($name)
    {
        $asset = $this->_resolveTemplatePath($name);

        return filemtime($asset);
    }
}
