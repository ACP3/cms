<?php
namespace ACP3\Modules\ACP3\Seo\View\Renderer\Smarty\Modifiers;

use ACP3\Core\Config;
use ACP3\Core\Lang;
use ACP3\Core\SEO;
use ACP3\Core\View\Renderer\Smarty\Modifiers\AbstractModifier;

/**
 * Class Robots
 * @package ACP3\Modules\ACP3\Seo\View\Renderer\Smarty\Modifiers
 */
class Robots extends AbstractModifier
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;
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
        $this->seo = $seo;
    }

    /**
     * @return array
     */
    private function _setReplaceParams()
    {
        return [
            sprintf($this->lang->t('seo', 'robots_use_system_default'), $this->seo->getRobotsSetting()),
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
        if (empty($this->replace) === true) {
            $this->replace = $this->_setReplaceParams($this->seo);
        }

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