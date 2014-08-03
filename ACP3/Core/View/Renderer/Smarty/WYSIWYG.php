<?php
namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Core;
use Symfony\Component\DependencyInjection\Container;

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
     * @var Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param $params
     * @throws \InvalidArgumentException
     * @return string
     */
    public function process($params)
    {
        $params['id'] = !empty($params['id']) ? $params['id'] : $params['name'];

        $className = "\\ACP3\\Core\\WYSIWYG\\" . CONFIG_WYSIWYG;

        if (class_exists($className)) {
            /** @var Core\WYSIWYG\AbstractWYSIWYG $wysiwyg */
            $wysiwyg = new $className();
            $wysiwyg->setContainer($this->container);
            $wysiwyg->setParameters($params);
            return $wysiwyg->display();
        } else {
            throw new \InvalidArgumentException('Can not find wysiwyg service ' . $serviceId);
        }
    }
}