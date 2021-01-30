<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

abstract class AbstractFrontendAction extends AbstractWidgetAction
{
    /**
     * @return $this
     */
    public function setLayout(string $layout): self
    {
        $this->view->setLayout($layout);

        return $this;
    }
}
