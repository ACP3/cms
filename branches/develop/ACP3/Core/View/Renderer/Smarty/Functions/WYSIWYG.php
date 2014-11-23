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

        $settings = $this->container->get('system.config')->getSettings();
        $className = "\\ACP3\\Core\\WYSIWYG\\" . $settings['wysiwyg'];

        if (class_exists($className)) {
            /** @var Core\WYSIWYG\AbstractWYSIWYG $wysiwyg */
            $wysiwyg = new $className();
            $wysiwyg->setContainer($this->container);
            $wysiwyg->setParameters($params);
            return $wysiwyg->display();
        } else {
            throw new \InvalidArgumentException('Can not find wysiwyg service ' . $className);
        }
    }
}