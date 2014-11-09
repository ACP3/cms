<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core;

/**
 * Class RedirectMessage
 * @package ACP3\Core\View\Renderer\Smarty\Plugins
 */
class RedirectMessage extends AbstractPlugin
{
    /**
     * @var Core\Helpers\RedirectMessages
     */
    protected $redirectMessages;
    /**
     * @var string
     */
    protected $pluginName = 'redirect_message';

    /**
     * @param Core\Helpers\RedirectMessages $redirectMessages
     */
    public function __construct(Core\Helpers\RedirectMessages $redirectMessages)
    {
        $this->redirectMessages = $redirectMessages;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function process(array $params)
    {
        return $this->redirectMessages->getMessage();
    }
}