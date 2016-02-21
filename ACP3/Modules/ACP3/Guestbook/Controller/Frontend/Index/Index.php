<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\Guestbook;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Guestbook\Controller\Frontend\Index
 */
class Index extends AbstractAction
{
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository
     */
    protected $guestbookRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Modules\Controller\FrontendContext          $context
     * @param \ACP3\Core\Pagination                                  $pagination
     * @param \ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository $guestbookRepository
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Pagination $pagination,
        Guestbook\Model\GuestbookRepository $guestbookRepository
    )
    {
        parent::__construct($context);

        $this->pagination = $pagination;
        $this->guestbookRepository = $guestbookRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $guestbook = $this->guestbookRepository->getAll($this->guestbookSettings['notify'], POS, $this->user->getEntriesPerPage());
        $c_guestbook = count($guestbook);

        if ($c_guestbook > 0) {
            $this->pagination->setTotalResults($this->guestbookRepository->countAll($this->guestbookSettings['notify']));

            for ($i = 0; $i < $c_guestbook; ++$i) {
                if ($this->guestbookSettings['emoticons'] == 1 && $this->emoticonsHelpers) {
                    $guestbook[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($guestbook[$i]['message']);
                }
            }
        }

        return [
            'guestbook' => $guestbook,
            'overlay' => $this->guestbookSettings['overlay'],
            'pagination' => $this->pagination->render(),
            'dateformat' => $this->guestbookSettings['dateformat']
        ];
    }
}
