<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;
use ACP3\Modules\ACP3\Seo\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InsertUriAliasOnModelAfterSaveListener implements EventSubscriberInterface
{
    public function __construct(private readonly ACL $acl, private readonly UriAliasManager $uriAliasManager)
    {
    }

    public function __invoke(ModelSaveEvent $event): void
    {
        if ($this->acl->hasPermission('admin/seo/index/create')) {
            $formData = $event->getRawData();

            if ($event->getModuleName() !== Schema::MODULE_NAME && !empty($formData['seo_uri_pattern'])) {
                $this->uriAliasManager->insertUriAlias(
                    sprintf($formData['seo_uri_pattern'], $event->getEntryId()),
                    $formData['alias'],
                    $formData['seo_keywords'],
                    $formData['seo_description'],
                    (int) $formData['seo_robots'],
                    $formData['seo_title'],
                    $formData['seo_structured_data'],
                    $formData['seo_canonical'],
                );
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.model.after_save' => '__invoke',
        ];
    }
}
