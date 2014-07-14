<?php

namespace ACP3\Core\Modules\Controller;

use ACP3\Core;

/**
 * Class Sidebar
 * @package ACP3\Core\Modules\Controller
 */
class Sidebar extends Core\Modules\Controller
{
    /**
     * @var string
     */
    protected $layout = '';

    /**
     * @return $this
     */
    public function preDispatch()
    {
        $this->setNoOutput(false);

        return $this;
    }

    public function display()
    {
        if ($this->getNoOutput() === false && $this->getLayout() !== '') {
            $this->view->displayTemplate($this->getLayout());
        }
    }

}
