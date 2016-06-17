<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Frontend\Index;

use ACP3\Core\Controller\AbstractFrontendAction as CoreAbstractFrontendAction;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class AbstractAction
 * @package ACP3\Modules\ACP3\Comments\Controller\Frontend\Index
 */
abstract class AbstractFrontendAction extends CoreAbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelpers;
    /**
     * @var bool
     */
    protected $emoticonsActive = false;
    /**
     * @var array
     */
    protected $commentsSettings = [];

    public function preDispatch()
    {
        parent::preDispatch();

        $this->commentsSettings = $this->config->getSettings('comments');
        $this->emoticonsActive = ($this->commentsSettings['emoticons'] == 1);
    }

    /**
     * @param \ACP3\Modules\ACP3\Emoticons\Helpers $emoticonsHelpers
     *
     * @return $this
     */
    public function setEmoticonsHelpers(Emoticons\Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;

        return $this;
    }
}
