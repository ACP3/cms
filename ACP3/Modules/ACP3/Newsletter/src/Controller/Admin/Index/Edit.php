<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Edit extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Newsletter\Model\NewsletterModel
     */
    private $newsletterModel;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\ViewProviders\AdminNewsletterEditViewProvider
     */
    private $adminNewsletterEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Newsletter\Model\NewsletterModel $newsletterModel,
        Newsletter\ViewProviders\AdminNewsletterEditViewProvider $adminNewsletterEditViewProvider
    ) {
        parent::__construct($context);

        $this->newsletterModel = $newsletterModel;
        $this->adminNewsletterEditViewProvider = $adminNewsletterEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id): array
    {
        $newsletter = $this->newsletterModel->getOneById($id);

        if (empty($newsletter) === false) {
            return ($this->adminNewsletterEditViewProvider)($newsletter);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
