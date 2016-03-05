<?php

namespace ACP3\Installer\Core\View\Renderer;

/**
 * Class Smarty
 * @package ACP3\Installer\Core\View\Renderer
 */
class Smarty extends \ACP3\Core\View\Renderer\Smarty
{
    /**
     * @param array $params
     *
     * @throws \SmartyException
     */
    public function configure(array $params = [])
    {
        $this->setErrorReporting($this->isDevOrInstall() ? E_ALL : 0);

        if (!empty($params['compile_id'])) {
            $this->setCompileId($params['compile_id']);
        }
    }
}
