<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Archive;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Details
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Archive
 */
class Details extends Core\Controller\FrontendController
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository
     */
    protected $newsletterRepository;

    /**
     * Details constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext            $context
     * @param \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository $newsletterRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Newsletter\Model\NewsletterRepository $newsletterRepository)
    {
        parent::__construct($context);

        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        $newsletter = $this->newsletterRepository->getOneById($id, 1);

        if (!empty($newsletter)) {
            $this->breadcrumb
                ->append($this->translator->t('newsletter', 'index'), 'newsletter')
                ->append($this->translator->t('newsletter', 'frontend_archive_index'), 'newsletter/archive')
                ->append($newsletter['title']);

            return [
                'newsletter' => $newsletter
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }
}
