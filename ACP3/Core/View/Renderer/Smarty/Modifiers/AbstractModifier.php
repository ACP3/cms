<?php
namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

use ACP3\Core\View\Renderer\Smarty\AbstractPlugin;
use ACP3\Core\View\Renderer\Smarty\PluginInterface;

/**
 * Class AbstractModifier
 * @package ACP3\Core\View\Renderer\Smarty\Modifiers
 */
abstract class AbstractModifier extends AbstractPlugin
{
    /**
     * @inheritdoc
     */
    public function getExtensionType()
    {
        return PluginInterface::EXTENSION_TYPE_MODIFIER;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    abstract public function process($value);
}
