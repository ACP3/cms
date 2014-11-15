<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core;

/**
 * Class Icon
 * @package ACP3\Core\View\Renderer\Smarty
 */
class Icon extends AbstractPlugin
{
    /**
     * @var Core\Assets\ThemeResolver
     */
    protected $themeResolver;
    /**
     * @var \ACP3\Core\Validator\Rules\Misc
     */
    protected $validate;
    /**
     * @var string
     */
    protected $pluginName = 'icon';

    /**
     * @param Core\Assets\ThemeResolver $themeResolver
     * @param Core\Validator\Rules\Misc $validate
     */
    public function __construct(
        Core\Assets\ThemeResolver $themeResolver,
        Core\Validator\Rules\Misc $validate
    )
    {
        $this->themeResolver = $themeResolver;
        $this->validate = $validate;
    }

    /**
     * @param $imageName
     * @param $width
     * @param $height
     * @return array
     */
    public function getImageDimensions($imageName, $width, $height)
    {
        $path = $this->themeResolver->getStaticAssetPath(
            'System/Resources/Assets/',
            'System/',
            'images/crystal_project',
            $imageName . '.png'
        );

        if ($this->validate->isNumber($width) === true && $this->validate->isNumber($height) === true) {
            $width = ' width="' . $width . '"';
            $height = ' height="' . $height . '"';
        } elseif (empty($path) === false) {
            $picInfos = getimagesize($path);
            $width = ' width="' . $picInfos[0] . '"';
            $height = ' height="' . $picInfos[1] . '"';
        } else {
            $width = $height = '';
        }

        return array(
            'path' => ROOT_DIR . substr($path, strpos($path, '/ACP3/Modules/') + 1),
            'width' => $width,
            'height' => $height
        );
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $imageInfo = $this->getImageDimensions(
            $params['path'],
            isset($params['width']) ? $params['width'] : '',
            isset($params['height']) ? $params['height'] : ''
        );

        $alt = ' alt="' . (!empty($params['alt']) ? $params['alt'] : '') . '"';
        $title = !empty($params['title']) ? ' title="' . $params['title'] . '"' : '';

        return '<img src="' . $imageInfo['path'] . '"' . $imageInfo['width'] . $imageInfo['height'] . $alt . $title . ' />';
    }
}