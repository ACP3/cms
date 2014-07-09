<?php
namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Core;

/**
 * Class URI
 * @package ACP3\Core\View\Renderer\Smarty
 */
class URI extends AbstractPlugin
{
    /**
     * @var Core\URI
     */
    protected $uri;
    /**
     * @var string
     */
    protected $pluginName = 'uri';

    public function __construct(Core\URI $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param $params
     * @return string
     */
    public function process($params)
    {
        return $this->uri->route(!empty($params['args']) ? $params['args'] : '');
    }
}