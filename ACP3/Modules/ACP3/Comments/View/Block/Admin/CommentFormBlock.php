<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\View\Block\Admin;

use ACP3\Core\View\Block\AbstractFormBlock;

class CommentFormBlock extends AbstractFormBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $this->modifyBreadcrumbAndTitle($data);

        return [
            'form' => array_merge($data, $this->getRequestData()),
            'module_id' => (int)$data['module_id'],
            'form_token' => $this->formToken->renderFormToken(),
            'can_use_emoticons' => true
        ];
    }

    /**
     * @param array $data
     */
    private function modifyBreadcrumbAndTitle(array $data)
    {
        $this->breadcrumb
            ->append(
                $this->translator->t($data['module'], $data['module']),
                'acp/comments/details/index/id_' . $data['module_id']
            )
            ->append($this->translator->t('comments', 'admin_details_edit'));

        $this->title->setPageTitlePrefix($data['name']);
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
