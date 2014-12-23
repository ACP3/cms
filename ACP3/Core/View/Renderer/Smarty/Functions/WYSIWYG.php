<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class WYSIWYG
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class WYSIWYG extends AbstractFunction
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'wysiwyg';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $params['id'] = !empty($params['id']) ? $params['id'] : $params['name'];

        $serviceId = 'core.wysiwyg.' . $this->container->get('system.config')->getSettings()['wysiwyg'];

        if ($this->container->has($serviceId) === true) {
            /** @var Core\WYSIWYG\AbstractWYSIWYG $wysiwyg */
            $wysiwyg = $this->container->get($serviceId);
            $wysiwyg->setParameters($params);
            return $wysiwyg->display();
        } else {
            throw new \InvalidArgumentException('Can not find wysiwyg service ' . $serviceId);
        }
    }
}
