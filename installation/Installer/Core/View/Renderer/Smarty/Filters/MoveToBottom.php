<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\View\Renderer\Smarty\Filters;

/**
 * Class MoveToBottom
 * @package ACP3\Installer\Core\View\Renderer\Smarty\Filters
 */
class MoveToBottom extends \ACP3\Core\View\Renderer\Smarty\Filters\MoveToBottom
{
    public function __construct()
    {
        ;
    }

    /**
     * @inheritdoc
     */
    protected function addElementFromMinifier()
    {
        return '';
    }
}
