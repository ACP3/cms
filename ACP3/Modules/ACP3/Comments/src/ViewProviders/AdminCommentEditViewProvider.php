<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

class AdminCommentEditViewProvider
{
    public function __construct(private FormToken $formTokenHelper, private RequestInterface $request, private Steps $breadcrumb, private Title $title, private Translator $translator)
    {
    }

    public function __invoke(array $comment): array
    {
        $this->breadcrumb
            ->append(
                $this->translator->t($comment['module'], $comment['module']),
                'acp/comments/details/index/id_' . $comment['module_id']
            )
            ->append(
                $this->translator->t('comments', 'admin_details_edit'),
                $this->request->getQuery()
            );

        $this->title->setPageTitlePrefix($comment['name']);

        return [
            'form' => array_merge($comment, $this->request->getPost()->all()),
            'module_id' => (int) $comment['module_id'],
            'form_token' => $this->formTokenHelper->renderFormToken(),
            'can_use_emoticons' => true,
        ];
    }
}
