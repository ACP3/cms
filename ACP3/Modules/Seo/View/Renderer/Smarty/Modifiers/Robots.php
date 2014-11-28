<?php
namespace ACP3\Modules\Seo\View\Renderer\Smarty\Modifiers;

use ACP3\Core\Config;
use ACP3\Core\Lang;
use ACP3\Core\SEO;
use ACP3\Core\View\Renderer\Smarty\Modifiers\AbstractModifier;

/**
 * Class Robots
 * @package ACP3\Modules\Seo\View\Renderer\Smarty\Modifiers
 */
class Robots extends AbstractModifier
{
    /**
     * @var Lang
     */
    protected $lang;
    /**
     * @var array
     */
    protected $search = [0, 1, 2, 3, 4];
    /**
     * @var array
     */
    protected $replace = [];

    /**
     * @param Lang $lang
     * @param SEO $seo
     */
    public function __construct(
        Lang $lang,
        SEO $seo)
    {
        $this->lang = $lang;

        $this->replace = $this->_setReplaceParams($seo);
    }

    /**
     * @param SEO $seo
     * @return array
     */
    private function _setReplaceParams(SEO $seo)
    {
        return [
            sprintf($this->lang->t('seo', 'robots_use_system_default'), $seo->getRobotsSetting()),
            $this->lang->t('seo', 'robots_index_follow'),
            $this->lang->t('seo', 'robots_index_nofollow'),
            $this->lang->t('seo', 'robots_noindex_follow'),
            $this->lang->t('seo', 'robots_noindex_nofollow')
        ];
    }

    /**
     * @inheritdoc
     */
    public function process($value)
    {
        return str_replace($this->search, $this->replace, $value);
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'robots';
    }
}