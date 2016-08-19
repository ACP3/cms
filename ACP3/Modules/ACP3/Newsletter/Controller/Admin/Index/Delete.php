<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var Newsletter\Model\NewsletterModel
     */
    protected $newsletterModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Newsletter\Model\NewsletterModel $newsletterModel
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Newsletter\Model\NewsletterModel $newsletterModel
    ) {
        parent::__construct($context);

        $this->newsletterModel = $newsletterModel;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->newsletterModel->delete($items);
            }
        );
    }
}
