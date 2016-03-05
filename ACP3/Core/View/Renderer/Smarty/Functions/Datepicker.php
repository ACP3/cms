<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Helpers\Date;

/**
 * Class Datepicker
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class Datepicker extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    protected $dateHelper;

    /**
     * Datepicker constructor.
     *
     * @param \ACP3\Core\Helpers\Date $dateHelper
     */
    public function __construct(Date $dateHelper)
    {
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
        $smarty->smarty->assign('datepicker', $this->dateHelper->datepicker(
            $params['name'],
            $params['value'],
            $params['withTime'],
            $params['inputFieldOnly']
        ));

        return $smarty->smarty->fetch('asset:System/datepicker.tpl');
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
    public function getExtensionName()
    {
        return 'datepicker';
    }
}
