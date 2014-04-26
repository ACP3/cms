<?php

namespace ACP3\Core\View;

/**
 * Abstract Class for the various renderers
 */
abstract class AbstractRenderer implements RendererInterface
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

    public function __construct(array $params = array())
    {
        $this->config = $params;
    }
}
