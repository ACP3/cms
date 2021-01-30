<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

/**
 * @deprecated since ACP3 version 5.15.0. To be removed with version 6.x. Use the AbstractWidgetAction instead
 * @see \ACP3\Core\Controller\AbstractWidgetAction
 */
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
