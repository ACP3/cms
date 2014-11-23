<?php
namespace ACP3\Core\View\Renderer\Smarty\Filters;

/**
 * Class AbstractFilter
 * @package ACP3\Core\View\Renderer\Smarty\Filters
 */
abstract class AbstractFilter
{
    /**
     * @var string
     */
    protected $filterType = '';

    /**
     * @param \Smarty $smarty
     */
    public function registerFilter(\Smarty $smarty)
    {
        $smarty->registerFilter($this->filterType, [$this, 'process']);
    }

    /**
     * @param $tpl_output
     * @param \Smarty_Internal_Template $smarty
     * @return string
     */
    abstract public function process($tpl_output, \Smarty_Internal_Template $smarty);
} 