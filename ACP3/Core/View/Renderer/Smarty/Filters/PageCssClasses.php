<?php
namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core;

/**
 * Class PageCssClasses
 * @package ACP3\Core\View\Renderer\Smarty\Filters
 */
class PageCssClasses extends AbstractFilter
{
    /**
     * @var string
     */
    protected $filterType = 'output';

    /**
     * @var \ACP3\Core\Assets\PageCssClasses
     */
    protected $pageCssClasses;
    /**
     * @var \ACP3\Core\RequestInterface
     */
    protected $request;
    /**
     * @var string
     */
    protected $cssClassCache = '';

    /**
     * @param \ACP3\Core\Assets\PageCssClasses $pageCssClasses
     * @param \ACP3\Core\RequestInterface      $request
     */
    public function __construct(
        Core\Assets\PageCssClasses $pageCssClasses,
        Core\RequestInterface $request)
    {
        $this->pageCssClasses = $pageCssClasses;
        $this->request = $request;
    }

    /**
     * @param                           $tpl_output
     * @param \Smarty_Internal_Template $smarty
     *
     * @return string
     */
    public function process($tpl_output, \Smarty_Internal_Template $smarty)
    {
        if (strpos($tpl_output, '<body') !== false) {
            if ($this->cssClassCache === '') {
                $pieces = [
                    $this->pageCssClasses->getModule(),
                    $this->pageCssClasses->getControllerAction()
                ];

                if ($this->request->getArea() === 'admin') {
                    $pieces[] = 'in-admin';
                } elseif ($this->request->getIsHomepage() === true) {
                    $pieces[] = 'is-homepage';
                } else {
                    $pieces[] = $this->pageCssClasses->getDetails();
                }

                $this->cssClassCache = 'class="' . implode(' ', $pieces) . '"';
            }

            $tpl_output = str_replace('<body', '<body ' . $this->cssClassCache, $tpl_output);
        }

        return $tpl_output;
    }
}