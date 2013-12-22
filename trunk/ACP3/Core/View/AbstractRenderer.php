<?php

namespace ACP3\Core\View;

/**
 * Abstract Class for the various renderers
 */
abstract class AbstractRenderer implements RendererInterface
{

    protected $config = array();
    public $renderer = null;

    public function __construct(array $params = array())
    {
        $this->config = $params;
    }
}
