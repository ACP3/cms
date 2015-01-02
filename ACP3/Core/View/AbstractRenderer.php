<?php

namespace ACP3\Core\View;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Abstract Class for the various renderers
 * @package ACP3\Core\View
 */
abstract class AbstractRenderer extends ContainerAware implements RendererInterface
{
    /**
     * The assigned layout renderer
     *
     * @var
     */
    public $renderer;
    /**
     * Possible configuration options for the current renderer
     *
     * @var array
     */
    protected $config = [];

    /**
     * @param array $params
     */
    public function configure(array $params = [])
    {
        $this->config = $params;
    }
}
