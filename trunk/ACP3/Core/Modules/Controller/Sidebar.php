<?php

namespace ACP3\Core\Modules\Controller;

use ACP3\Core;

/**
 * Description of AdminController
 *
 * @author goratsch
 */
class Sidebar extends Core\Modules\Controller
{
    /**
     * @var string
     */
    protected $layout = '';

    public function display()
    {
        if ($this->getNoOutput() === false && $this->getLayout() !== '') {
            $this->view->displayTemplate($this->getLayout());
        }
    }

}
