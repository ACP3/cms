<?php
namespace ACP3\Core\View\Renderer\Smarty;

/**
 * Class AbstractPlugin
 * @package ACP3\Core\View\Renderer\Smarty
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @inheritdoc
     */
    public function register(\Smarty $smarty)
    {
        if ($this->getExtensionType() === static::EXTENSION_TYPE_BLOCK ||
            $this->getExtensionType() === static::EXTENSION_TYPE_FUNCTION ||
            $this->getExtensionType() === static::EXTENSION_TYPE_MODIFIER) {
            $smarty->registerPlugin($this->getExtensionType(), $this->getExtensionName(), [$this, 'process']);
        } else if ($this->getExtensionType() === static::EXTENSION_TYPE_FILTER) {
            $smarty->registerFilter($this->getExtensionName(), [$this, 'process']);
        }
    }
}
