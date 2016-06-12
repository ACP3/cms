<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Archive;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Archive
 */
class Index extends Core\Controller\FrontendAction
{
    use Core\Cache\CacheResponseTrait;
    
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository
     */
    protected $newsletterRepository;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext            $context
     * @param Core\Pagination                                          $pagination
     * @param \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository $newsletterRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Pagination $pagination,
        Newsletter\Model\NewsletterRepository $newsletterRepository)
    {
        parent::__construct($context);

        $this->pagination = $pagination;
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $this->setCacheResponseCacheable($this->config->getSettings('system')['cache_lifetime']);

        $this->pagination->setTotalResults($this->newsletterRepository->countAll(1));

        return [
            'newsletters' => $this->newsletterRepository->getAll(1, POS, $this->user->getEntriesPerPage()),
            'pagination' => $this->pagination->render()
        ];
    }
}
