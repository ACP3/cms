<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Archive;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

class Details extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Repository\NewsletterRepository
     */
    private $newsletterRepository;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\ViewProviders\NewsletterDetailsViewProvider
     */
    private $newsletterDetailsViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Newsletter\Repository\NewsletterRepository $newsletterRepository,
        Newsletter\ViewProviders\NewsletterDetailsViewProvider $newsletterDetailsViewProvider
    ) {
        parent::__construct($context);

        $this->newsletterRepository = $newsletterRepository;
        $this->newsletterDetailsViewProvider = $newsletterDetailsViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id): Response
    {
        $newsletter = $this->newsletterRepository->getOneByIdAndStatus($id, 1);

        if (!empty($newsletter)) {
            $response = $this->renderTemplate(null, ($this->newsletterDetailsViewProvider)($newsletter));
            $this->setCacheResponseCacheable($response, $this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            return $response;
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
