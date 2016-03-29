<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller\Event;


use ACP3\Core\View;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CustomTemplateVariableEvent
 * @package ACP3\Core\Controller\Event
 */
class CustomTemplateVariableEvent extends Event
{
    /**
     * @var \ACP3\Core\View
     */
    private $view;

    /**
     * CustomTemplateVariableEvent constructor.
     *
     * @param \ACP3\Core\View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * @return \ACP3\Core\View
     */
    public function getView()
    {
        return $this->view;
    }
}
