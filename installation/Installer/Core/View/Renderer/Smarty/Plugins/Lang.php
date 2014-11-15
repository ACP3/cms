<?php
namespace ACP3\Installer\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core\View\Renderer\Smarty\Plugins\AbstractPlugin;

/**
 * Class Lang
 * @package ACP3\Installer\Core\View\Renderer\Smarty
 */
class Lang extends AbstractPlugin
{
    /**
     * @var \ACP3\Installer\Core\Lang
     */
    protected $lang;
    /**
     * @var string
     */
    protected $pluginName = 'lang';

    /**
     * @param \ACP3\Installer\Core\Lang $lang
     */
    public function __construct(\ACP3\Installer\Core\Lang $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $values = explode('|', $params['t']);
        return $this->lang->t($values[0], $values[1]);
    }
}