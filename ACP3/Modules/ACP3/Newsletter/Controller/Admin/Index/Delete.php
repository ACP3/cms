<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Newsletter\Model\NewsletterModel
     */
    protected $newsletterModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Newsletter\Model\NewsletterModel $newsletterModel
    ) {
        parent::__construct($context);

        $this->newsletterModel = $newsletterModel;
    }

    /**
     * @return mixed
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(string $action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->newsletterModel->delete($items);
            }
        );
    }
}
