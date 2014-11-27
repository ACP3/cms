<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class Lang
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class Lang extends AbstractFunction
{
    /**
     * @var Core\Lang
     */
    protected $lang;

    /**
     * @param Core\Lang $lang
     */
    public function __construct(Core\Lang $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'lang';
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
