<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;
use ACP3\Modules\ACP3\Seo\Installer\Schema;

class InsertUriAliasOnModelAfterSaveListener
{
    /**
     * @var UriAliasManager
     */
    private $uriAliasManager;
    /**
     * @var ACLInterface
     */
    private $acl;

    /**
     * InsertUriAliasOnModelAfterSaveListener constructor.
     * @param ACLInterface $acl
     * @param UriAliasManager $uriAliasManager
     */
    public function __construct(
        ACLInterface $acl,
        UriAliasManager $uriAliasManager
    ) {
        $this->uriAliasManager = $uriAliasManager;
        $this->acl = $acl;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function insertUriAlias(ModelSaveEvent $event)
    {
        if ($this->acl->hasPermission('admin/seo/index/manage')) {
            $formData = $event->getRawData();

            if ($event->getModuleName() !== Schema::MODULE_NAME && !empty($formData['seo_uri_pattern'])) {
                $this->uriAliasManager->insertUriAlias(
                    \sprintf($formData['seo_uri_pattern'], $event->getEntryId()),
                    $formData['alias'],
                    $formData['seo_keywords'],
                    $formData['seo_description'],
                    (int)$formData['seo_robots'],
                    $formData['seo_title']
                );
            }
        }
    }
}
