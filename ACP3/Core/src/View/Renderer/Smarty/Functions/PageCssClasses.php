<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Assets;

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
    public function getExtensionName()
    {
        return 'page_css_classes';
    }
}
