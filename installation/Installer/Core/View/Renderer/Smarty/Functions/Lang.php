<?php
namespace ACP3\Installer\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\View\Renderer\Smarty\Functions\AbstractFunction;

/**
 * Class Lang
 * @package ACP3\Installer\Core\View\Renderer\Smarty\Functions
 */
class Lang extends AbstractFunction
{
    /**
     * @var \ACP3\Installer\Core\I18n\Translator
     */
    protected $translator;

    /**
     * @param \ACP3\Installer\Core\I18n\Translator $translator
     */
    public function __construct(\ACP3\Installer\Core\I18n\Translator $translator)
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
        return $this->translator->t($values[0], $values[1]);
    }
}
