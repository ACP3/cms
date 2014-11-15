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

    /**
     * @param Core\Assets\ThemeResolver $themeResolver
     */
    public function __construct(Core\Assets\ThemeResolver $themeResolver)
    {
        $this->themeResolver = $themeResolver;
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->themeResolver->resolveTemplatePath($params['path']);
    }
}