<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\System\Services\CacheClearService;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\Repository\CommentRepository
     */
    private $commentRepository;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;
    /**
     * @var \ACP3\Modules\ACP3\System\Services\CacheClearService
     */
    private $cacheClearService;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        Comments\Model\Repository\CommentRepository $commentRepository,
        CacheClearService $cacheClearService
    ) {
        parent::__construct($context);

        $this->commentRepository = $commentRepository;
        $this->actionHelper = $actionHelper;
        $this->cacheClearService = $cacheClearService;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(?string $action = null)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                $bool = false;
                foreach ($items as $item) {
                    $bool = $this->commentRepository->delete($item, 'module_id');
                }

                $this->cacheClearService->clearCacheByType('page');

                return $bool;
            }
        );
    }
}
