<?php
namespace ACP3\Core\View\Renderer\Smarty\Resources;

use ACP3\Core;

/**
 * Class Asset
 * @package ACP3\Core\View\Renderer\Smarty\Resource
 */
class Asset extends AbstractResource
{
    /**
     * @var Core\Assets\ThemeResolver
     */
    protected $themeResolver;
    /**
     * @var string
     */
    protected $resourceName = 'asset';

    /**
     * @param Core\Assets\ThemeResolver $themeResolver
     */
    public function __construct(Core\Assets\ThemeResolver $themeResolver)
    {
        $this->themeResolver = $themeResolver;
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
        $asset = $this->themeResolver->resolveTemplatePath($name);

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
     * @return integer timestamp (epoch) the template was modified
     */
    protected function fetchTimestamp($name)
    {
        $asset = $this->themeResolver->resolveTemplatePath($name);

        return filemtime($asset);
    }
}