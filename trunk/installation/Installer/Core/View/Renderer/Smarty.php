<?php

namespace ACP3\Installer\Core\View\Renderer;

/**
 * Renderer for the Smarty template engine
 *
 * @package ACP3\Core\View\Renderer
 */
class Smarty extends \ACP3\Core\View\Renderer\Smarty
{

    /**
     * @param array $params
     * @throws \SmartyException
     */
    public function configure(array $params = array())
    {
        parent::configure($params);

        $this->renderer->setTemplateDir($params['template_dir']);
    }
}