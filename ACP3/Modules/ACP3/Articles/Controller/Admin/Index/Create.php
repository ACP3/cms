<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

class Create extends AbstractFormAction
{
    public function execute(?int $id)
    {
        if (!empty($id)) {
            throw new \InvalidArgumentException();
        }

        return parent::execute($id);
    }

    public function executePost(?int $id)
    {
        if (!empty($id)) {
            throw new \InvalidArgumentException();
        }

        return parent::executePost($id);
    }
}
