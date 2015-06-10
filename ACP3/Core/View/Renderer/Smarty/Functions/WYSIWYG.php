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
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
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

        $serviceId = $this->container->get('core.config')->getSettings('system')['wysiwyg'];

        if ($this->container->has($serviceId) === true) {
            /** @var Core\WYSIWYG\AbstractWYSIWYG $wysiwyg */
            $wysiwyg = $this->container->get($serviceId);

            if ($wysiwyg instanceof Core\WYSIWYG\AbstractWYSIWYG) {
                $wysiwyg->setParameters($params);
                return $wysiwyg->display();
            }

            throw new \InvalidArgumentException(get_class($wysiwyg) . ' has to extend the AbstractWYSIWYG class');
        }

        throw new \InvalidArgumentException('Can not find wysiwyg service ' . $serviceId);
    }
}