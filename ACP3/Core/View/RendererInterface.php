<?php

namespace ACP3\Core\View;

interface RendererInterface
{
    public function assign($name, $value = null);

    public function fetch($template);

    public function display($template);

    public function templateExists($template);
}
