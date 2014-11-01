<?php
namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

/**
 * Class AbstractModifier
 * @package ACP3\Core\View\Renderer\Smarty\Modifiers
 */
abstract class AbstractModifier
{
    /**
     * @var string
     */
    protected $modifierName = '';

    /**
     * @param \Smarty $smarty
     */
    public function registerModifier(\Smarty $smarty)
    {
        $smarty->registerPlugin('modifier', $this->modifierName, array($this, 'process'));
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    abstract public function process($params);
} 