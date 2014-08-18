<?php
namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Core;

/**
 * Class Icon
 * @package ACP3\Core\View\Renderer\Smarty
 */
class Icon extends AbstractPlugin
{
    /**
     * @var \ACP3\Core\Validator\Rules\Misc
     */
    protected $validate;
    /**
     * @var string
     */
    protected $pluginName = 'icon';

    public function __construct(Core\Validator\Rules\Misc $validate)
    {
        $this->validate = $validate;
    }

    /**
     * @param $params
     *
     * @return string
     */
    public function process($params)
    {
        $path = ROOT_DIR . CONFIG_ICONS_PATH . $params['path'] . '.png';
        $width = $height = '';

        if (!empty($params['width']) && !empty($params['height']) &&
            $this->validate->isNumber($params['width']) === true && $this->validate->isNumber($params['height']) === true
        ) {
            $width = ' width="' . $params['width'] . '"';
            $height = ' height="' . $params['height'] . '"';
        } elseif (is_file(ACP3_ROOT_DIR . $path) === true) {
            $picInfos = getimagesize(ACP3_ROOT_DIR . $path);
            $width = ' width="' . $picInfos[0] . '"';
            $height = ' height="' . $picInfos[1] . '"';
        }

        $alt = ' alt="' . (!empty($params['alt']) ? $params['alt'] : '') . '"';
        $title = !empty($params['title']) ? ' title="' . $params['title'] . '"' : '';

        return '<img src="' . $path . '"' . $width . $height . $alt . $title . ' />';
    }
}