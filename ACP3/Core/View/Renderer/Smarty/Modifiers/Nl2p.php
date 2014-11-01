<?php
namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

use ACP3\Core\Helpers\StringFormatter;

/**
 * Class Nl2p
 * @package ACP3\Core\View\Renderer\Smarty\Modifiers
 */
class Nl2p extends AbstractModifier
{
    /**
     * @var string
     */
    protected $modifierName = 'nl2p';
    /**
     * @var StringFormatter
     */
    protected $stringFormatter;

    /**
     * @param StringFormatter $stringFormatter
     */
    public function __construct(StringFormatter $stringFormatter)
    {
        $this->stringFormatter = $stringFormatter;
    }

    /**
     * @param $value
     * @return string
     */
    public function process($value)
    {
        return $this->stringFormatter->nl2p($value);
    }
}