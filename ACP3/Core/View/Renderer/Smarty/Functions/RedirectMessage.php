<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class RedirectMessage
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class RedirectMessage extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Helpers\RedirectMessages
     */
    protected $redirectMessages;

    /**
     * @param \ACP3\Core\Helpers\RedirectMessages $redirectMessages
     */
    public function __construct(Core\Helpers\RedirectMessages $redirectMessages)
    {
        $this->redirectMessages = $redirectMessages;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'redirect_message';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $smarty->smarty->assign('redirect', $this->redirectMessages->getMessage());

        return $smarty->smarty->fetch('asset:system/redirect_message.tpl');
    }
}
