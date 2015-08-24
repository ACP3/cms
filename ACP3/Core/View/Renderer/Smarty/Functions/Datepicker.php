<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Helpers\Date;
use ACP3\Core\View;

/**
 * Class Datepicker
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class Datepicker extends AbstractFunction
{
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    protected $dateHelper;

    /**
     * @param \ACP3\Core\View         $view
     * @param \ACP3\Core\Helpers\Date $dateHelper
     */
    public function __construct(
        View $view,
        Date $dateHelper
    )
    {
        $this->view = $view;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @param array                     $params
     * @param \Smarty_Internal_Template $smarty
     *
     * @return string
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $params = $this->mergeParameters($params);
        $this->view->assign('datepicker', $this->dateHelper->datepicker(
            $params['name'],
            $params['value'],
            $params['withTime'],
            $params['inputFieldOnly']
        ));

        return $this->view->fetchTemplate('system/datepicker.tpl');
    }

    private function mergeParameters(array $params)
    {
        $defaults = [
            'name' => '',
            'value' => '',
            'withTime' => true,
            'inputFieldOnly' => false
        ];
        return array_merge($defaults, $params);
    }

    /**
     * @return string
     */
    public function getPluginName()
    {
        return 'datepicker';
    }
}