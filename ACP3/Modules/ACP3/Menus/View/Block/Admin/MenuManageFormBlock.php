<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\View\Block\Admin;

use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;

class MenuManageFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $this->breadcrumb->setLastStepReplacement(
            $this->translator->t('menus', !$this->getId() ? 'admin_index_create' : 'admin_index_edit')
        );

        $this->title->setPageTitlePrefix($data['title']);

        return [
            'form' => \array_merge($data, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return ['index_name' => '', 'title' => ''];
    }
}
