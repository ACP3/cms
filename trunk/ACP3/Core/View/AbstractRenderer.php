<?php

namespace ACP3\Core\View;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Abstract Class for the various renderers
 */
abstract class AbstractRenderer extends ContainerAware implements RendererInterface
{

    /**
     * Possible configuration options for the current renderer
     *
     * @var array
     */
    protected $config = array();
    /**
     * The assigned layout renderer
     *
     * @var
     */
    public $renderer;

    /**
     * @param array $params
     */
    public function configure(array $params = array())
    {
        $this->config = $params;
    }
}
