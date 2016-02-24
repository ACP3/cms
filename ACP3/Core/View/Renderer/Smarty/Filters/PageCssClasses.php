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
     * @var \ACP3\Core\Assets\PageCssClasses
     */
    protected $pageCssClasses;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var string
     */
    protected $cssClassCache = '';

    /**
     * @param \ACP3\Core\Assets\PageCssClasses $pageCssClasses
     * @param \ACP3\Core\Http\RequestInterface $request
     */
    public function __construct(
        Core\Assets\PageCssClasses $pageCssClasses,
        Core\Http\RequestInterface $request)
    {
        $this->pageCssClasses = $pageCssClasses;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'output';
    }

    /**
     * @inheritdoc
     */
    public function process($tplOutput, \Smarty_Internal_Template $smarty)
    {
        if (strpos($tplOutput, '<body') !== false) {
            if ($this->cssClassCache === '') {
                $pieces = [
                    $this->pageCssClasses->getModule(),
                    $this->pageCssClasses->getControllerAction()
                ];

                if ($this->request->getArea() === Core\Controller\AreaEnum::AREA_ADMIN) {
                    $pieces[] = 'in-admin';
                } elseif ($this->request->isHomepage() === true) {
                    $pieces[] = 'is-homepage';
                } else {
                    $pieces[] = $this->pageCssClasses->getDetails();
                }

                $this->cssClassCache = 'class="' . implode(' ', $pieces) . '"';
            }

            $tplOutput = str_replace('<body', '<body ' . $this->cssClassCache, $tplOutput);
        }

        return $tplOutput;
    }
}