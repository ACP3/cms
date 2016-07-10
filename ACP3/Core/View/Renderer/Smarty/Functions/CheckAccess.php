<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class CheckAccess
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class CheckAccess extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Helpers\View\CheckAccess
     */
    protected $checkAccess;

    /**
     * @param \ACP3\Core\Helpers\View\CheckAccess $checkAccess
     */
    public function __construct(Core\Helpers\View\CheckAccess $checkAccess)
    {
        $this->checkAccess = $checkAccess;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'check_access';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $return = $this->checkAccess->outputLinkOrButton($params);

        if (is_array($return)) {
            $smarty->smarty->assign('access_check', $return);

            return $smarty->smarty->fetch('asset:System/Partials/access_check.tpl');
        }

        return $return;
    }
}
