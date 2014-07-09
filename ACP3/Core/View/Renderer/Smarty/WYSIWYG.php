<?php
namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Core;

/**
 * Class CheckAccess
 * @package ACP3\Core\View\Renderer\Smarty
 */
class WYSIWYG extends AbstractPlugin
{
    /**
     * @var string
     */
    protected $pluginName = 'wysiwyg';

    /**
     * @param $params
     * @return string
     */
    public function process($params)
    {
        $params['id'] = !empty($params['id']) ? $params['id'] : $params['name'];

        $wysiwyg = new Core\WYSIWYG();
        Core\WYSIWYG::factory(CONFIG_WYSIWYG, $params);
        return $wysiwyg->display();
    }
}