<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Frontend\Index;

use ACP3\Core\Controller\FrontendAction;

/**
 * Class AbstractAction
 * @package ACP3\Modules\ACP3\News\Controller\Frontend\Index
 */
class AbstractAction extends FrontendAction
{
    /**
     * @var array
     */
    protected $newsSettings = [];
    /**
     * @var bool
     */
    protected $commentsActive = false;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->newsSettings = $this->config->getSettings('news');
        $this->commentsActive = ($this->newsSettings['comments'] == 1 && $this->acl->hasPermission('frontend/comments') === true);
    }
}
