<?php
namespace ACP3\Installer\Core\View\Renderer\Smarty;

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

    public function __construct(\ACP3\Installer\Core\Lang $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function process(array $params)
    {
        $values = explode('|', $params['t']);
        return $this->lang->t($values[0], $values[1]);
    }
}