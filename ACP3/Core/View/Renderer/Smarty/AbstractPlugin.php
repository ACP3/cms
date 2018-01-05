<?php
namespace ACP3\Core\View\Renderer\Smarty;

abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @inheritdoc
     */
    public function register(\Smarty $smarty)
    {
        if ($this->isPlugin()) {
            $smarty->registerPlugin($this->getExtensionType(), $this->getExtensionName(), [$this, 'process']);
        } elseif ($this->getExtensionType() === static::EXTENSION_TYPE_FILTER) {
            $smarty->registerFilter($this->getExtensionName(), [$this, 'process']);
        }
    }

    /**
     * @return bool
     */
    private function isPlugin()
    {
        return \in_array(
            $this->getExtensionType(),
            [static::EXTENSION_TYPE_BLOCK, static::EXTENSION_TYPE_FUNCTION, static::EXTENSION_TYPE_MODIFIER]
        );
    }
}
