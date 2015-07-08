<?php
namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

use ACP3\Core\View\Renderer\Smarty\AbstractPlugin;

/**
 * Class AbstractModifier
 * @package ACP3\Core\View\Renderer\Smarty\Modifiers
 */
abstract class AbstractModifier extends AbstractPlugin
{
    /**
     * @inheritdoc
     */
    public function getPluginType()
    {
        return 'modifier';
    }

    /**
     * @param $params
     *
     * @return string
     */
    abstract public function process($params);
}
