<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Event;

use ACP3\Core\View;
use Symfony\Contracts\EventDispatcher\Event;

class CustomTemplateVariableEvent extends Event
{
    public const NAME = 'core.controller.custom_template_variable';

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
