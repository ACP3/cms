<?php
namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core;

/**
 * Class BodyCssClasses
 * @package ACP3\Core\View\Renderer\Smarty\Filters
 */
class BodyCssClasses extends AbstractFilter
{
    /**
     * @var string
     */
    protected $filterType = 'output';

    /**
     * @var \ACP3\Core\Breadcrumb
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\Request
     */
    protected $request;
    /**
     * @var string
     */
    protected $cssClassCache = '';

    /**
     * @param \ACP3\Core\Breadcrumb $breadcrumb
     * @param \ACP3\Core\Request    $request
     */
    public function __construct(
        Core\Breadcrumb $breadcrumb,
        Core\Request $request)
    {
        $this->breadcrumb = $breadcrumb;
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
                    $this->request->mod,
                    $this->request->mod . '-' . $this->request->controller . '-' . $this->request->file
                ];

                if ($this->request->getIsHomepage() === true) {
                    $pieces[] = 'is-homepage';
                } else {
                    $pageTitle = \Patchwork\Utf8::toAscii(
                        html_entity_decode(
                            str_replace(
                                ' ',
                                '-',
                                strtolower($this->breadcrumb->getPageTitle())
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    );
                    $pieces[] = $this->request->mod . '-' . $this->request->controller . '-' . $pageTitle;
                }

                if ($this->request->area === 'admin') {
                    $pieces[] = 'in-admin';
                }

                $this->cssClassCache = 'class="' . implode(' ', $pieces) . '"';
            }

            $tpl_output = str_replace('<body', '<body ' . $this->cssClassCache, $tpl_output);
        }

        return $tpl_output;
    }
}