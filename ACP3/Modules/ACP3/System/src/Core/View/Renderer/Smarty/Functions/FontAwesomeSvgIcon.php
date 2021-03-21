<?php


namespace ACP3\Modules\ACP3\System\Core\View\Renderer\Smarty\Functions;


use ACP3\Core\Assets\FileResolver;
use ACP3\Core\View\Renderer\Smarty\Functions\AbstractFunction;

class FontAwesomeSvgIcon extends AbstractFunction
{
    /**
     * @var FileResolver
     */
    private $fileResolver;

    public function __construct(FileResolver $fileResolver)
    {
        $this->fileResolver = $fileResolver;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): string
    {
        if (isset($params['iconSet'], $params['icon'])) {
            $iconSet = $params['iconSet'];
            $icon = $params['icon'];

            $path = $this->fileResolver->getWebStaticAssetPath('system', 'Assets/sprites', $iconSet . '.svg');

            return <<<HTML
<svg class="svg-icon svg-icon__$icon"><use xlink:href="$path#$icon"></use></svg>
HTML;
        }

        throw new \InvalidArgumentException(\sprintf('Not all necessary arguments for the function %s were passed!', __FUNCTION__));
    }
}
