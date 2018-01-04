<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\View\Block\Admin;

use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;

class EmoticonManageFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        $this->setTemplate($this->getId() ? 'Emoticons/Admin/index.edit.tpl' : 'Emoticons/Admin/index.create.tpl');

        $this->breadcrumb->setLastStepReplacement(
            $this->translator->t('emoticons', !$this->getId() ? 'admin_index_create' : 'admin_index_edit')
        );

        return [
            'form' => array_merge($this->getData(), $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return ['code' => '', 'description' => ''];
    }
}
