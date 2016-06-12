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
    use Core\Cache\CacheResponseTrait;

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
     * @param \ACP3\Core\Controller\Context\FrontendContext          $context
     * @param \ACP3\Core\Pagination                                  $pagination
     * @param \ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository $guestbookRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Pagination $pagination,
        Guestbook\Model\GuestbookRepository $guestbookRepository
    ) {
        parent::__construct($context);

        $this->pagination = $pagination;
        $this->guestbookRepository = $guestbookRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $this->setCacheResponseCacheable($this->config->getSettings('system')['cache_lifetime']);

        $guestbook = $this->guestbookRepository->getAll($this->guestbookSettings['notify'], POS, $this->user->getEntriesPerPage());
        $cGuestbook = count($guestbook);

        if ($cGuestbook > 0) {
            $this->pagination->setTotalResults($this->guestbookRepository->countAll($this->guestbookSettings['notify']));

            for ($i = 0; $i < $cGuestbook; ++$i) {
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
