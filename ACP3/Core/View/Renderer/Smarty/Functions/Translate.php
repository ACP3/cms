<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class Translate
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class Translate extends AbstractFunction
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;

    /**
     * @param \ACP3\Core\I18n\Translator $translator
     */
    public function __construct(Core\I18n\Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'lang';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $values = explode('|', $params['t']);
        $params = isset($params['args']) && is_array($params['args']) ? $params['args'] : [];
        return $this->translator->t($values[0], $values[1], $params);
    }
}
