<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Details;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

class Edit extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Comments\Model\CommentsModel
     */
    private $commentsModel;
    /**
     * @var \ACP3\Modules\ACP3\Comments\ViewProviders\AdminCommentEditViewProvider
     */
    private $adminCommentEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Comments\Model\CommentsModel $commentsModel,
        Comments\ViewProviders\AdminCommentEditViewProvider $adminCommentEditViewProvider
    ) {
        parent::__construct($context);

        $this->commentsModel = $commentsModel;
        $this->adminCommentEditViewProvider = $adminCommentEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id): array
    {
        $comment = $this->commentsModel->getOneById($id);

        if (empty($comment) === false) {
            return ($this->adminCommentEditViewProvider)($comment);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
