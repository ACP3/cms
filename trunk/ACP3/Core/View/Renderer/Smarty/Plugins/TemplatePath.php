<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core;

/**
 * Class TemplatePath
 * @package ACP3\Core\View\Renderer\Smarty\Plugins
 */
class TemplatePath extends AbstractPlugin
{
    /**
     * @var Core\Assets\ThemeResolver
     */
    protected $themeResolver;
    /**
     * @var string
     */
    protected $pluginName = 'template_path';

    public function __construct(Core\Assets\ThemeResolver $themeResolver)
    {
        $this->themeResolver = $themeResolver;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function process(array $params)
    {
        return $this->themeResolver->resolveTemplatePath($params['path']);
    }
}