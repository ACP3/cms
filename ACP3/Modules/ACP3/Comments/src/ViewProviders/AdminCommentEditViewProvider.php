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
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        FormToken $formTokenHelper,
        RequestInterface $request,
        Steps $breadcrumb,
        Title $title,
        Translator $translator
    ) {
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
        $this->translator = $translator;
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
