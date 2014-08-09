<?php
namespace ACP3\Installer\Core\View\Renderer\Smarty;

use ACP3\Core\View\Renderer\Smarty\AbstractPlugin;

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
     * @param $params
     * @return string
     */
    public function process($params)
    {
        $values = explode('|', $params['t']);
        return $this->lang->t($values[0], $values[1]);
    }
}