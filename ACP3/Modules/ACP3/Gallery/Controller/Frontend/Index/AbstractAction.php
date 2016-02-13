<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;


use ACP3\Core\Modules\FrontendController;

/**
 * Class AbstractAction
 * @package ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index
 */
abstract class AbstractAction extends FrontendController
{
    protected $settings = [];

    public function preDispatch()
    {
        parent::preDispatch();

        $this->settings = $this->config->getSettings('gallery');
    }
}