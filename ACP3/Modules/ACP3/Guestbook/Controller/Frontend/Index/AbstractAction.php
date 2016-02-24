<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Frontend\Index;

use ACP3\Core\Controller\FrontendAction;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class AbstractAction
 * @package ACP3\Modules\ACP3\Guestbook\Controller\Frontend\Index
 */
abstract class AbstractAction extends FrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelpers;
    /**
     * @var array
     */
    protected $guestbookSettings = [];

    public function preDispatch()
    {
        parent::preDispatch();

        $this->guestbookSettings = $this->config->getSettings('guestbook');
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