<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\View\Block\Admin;

use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;
use ACP3\Modules\ACP3\Gallery\Helpers;

class GalleryManageFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $this->breadcrumb->setLastStepReplacement(
            $this->translator->t('gallery', !$this->getId() ? 'admin_index_create' : 'admin_index_edit')
        );

        $this->title->setPageTitlePrefix($data['title']);

        return [
            'gallery_id' => $data['id'],
            'form' => \array_merge($data, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
            'SEO_URI_PATTERN' => Helpers::URL_KEY_PATTERN_GALLERY,
            'SEO_ROUTE_NAME' => $this->getSeoRouteName($this->getId()),
        ];
    }

    /**
     * @param int|null $id
     * @return string
     */
    private function getSeoRouteName(?int $id): string
    {
        return !empty($id) ? \sprintf(Helpers::URL_KEY_PATTERN_GALLERY, $id) : '';
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [
            'id' => '',
            'title' => '',
            'start' => '',
            'end' => '',
        ];
    }
}