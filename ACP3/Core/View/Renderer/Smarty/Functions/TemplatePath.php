<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class TemplatePath
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class TemplatePath extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Assets\ThemeResolver
     */
    protected $themeResolver;

    /**
     * @param \ACP3\Core\Assets\ThemeResolver $themeResolver
     */
    public function __construct(Core\Assets\ThemeResolver $themeResolver)
    {
        $this->themeResolver = $themeResolver;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'template_path';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->themeResolver->resolveTemplatePath($params['path']);
    }
}
