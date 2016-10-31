<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;


use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

class InsertUriAliasOnModelAfterSaveListener
{
    /**
     * @var UriAliasManager
     */
    private $uriAliasManager;

    /**
     * InsertUriAliasOnModelAfterSaveListener constructor.
     * @param UriAliasManager $uriAliasManager
     */
    public function __construct(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function insertUriAlias(ModelSaveEvent $event)
    {
        $formData = $event->getRawData();

        if (!empty($formData['seo_uri_pattern'])) {
            $this->uriAliasManager->insertUriAlias(
                sprintf($formData['seo_uri_pattern'], $event->getEntryId()),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );
        }
    }
}
