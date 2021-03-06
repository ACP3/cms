<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Helpers\Date;

class Datepicker extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    protected $dateHelper;

    /**
     * Datepicker constructor.
     */
    public function __construct(Date $dateHelper)
    {
        $this->dateHelper = $dateHelper;
    }

    /**
     * @return string
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $params = $this->mergeParameters($params);

        $smarty->smarty->assign('label', $params['label']);
        $smarty->smarty->assign('datepicker', $this->dateHelper->datepicker(
            $params['name'],
            $params['value'],
            $params['withTime'],
            $params['inputFieldOnly']
        ));

        return $smarty->smarty->fetch('asset:System/Partials/datepicker.tpl');
    }

    private function mergeParameters(array $params)
    {
        $defaults = [
            'name' => '',
            'value' => '',
            'withTime' => true,
            'inputFieldOnly' => false,
            'label' => '',
        ];

        return array_merge($defaults, $params);
    }
}
