<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Assets;

/**
 * Class PageCssClasses
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class PageCssClasses extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Assets\PageCssClasses
     */
    protected $pageCssClasses;

    /**
     * @param \ACP3\Core\Assets\PageCssClasses $pageCssClasses
     */
    public function __construct(Assets\PageCssClasses $pageCssClasses)
    {
        $this->pageCssClasses = $pageCssClasses;
    }

    /**
     * @param array                     $params
     * @param \Smarty_Internal_Template $smarty
     *
     * @return mixed
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        if (isset($params['type'])) {
            switch ($params['type']) {
                case 'module':
                    return $this->pageCssClasses->getModule();
                case 'controllerAction':
                    return $this->pageCssClasses->getControllerAction();
                case 'details':
                    return $this->pageCssClasses->getDetails();
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function getPluginName()
    {
        return 'page_css_classes';
    }
}