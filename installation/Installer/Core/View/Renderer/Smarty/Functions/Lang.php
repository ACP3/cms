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
    protected $lang;

    /**
     * @param \ACP3\Installer\Core\I18n\Translator $lang
     */
    public function __construct(\ACP3\Installer\Core\I18n\Translator $lang)
    {
        $this->lang = $lang;
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
        return $this->lang->t($values[0], $values[1]);
    }
}
