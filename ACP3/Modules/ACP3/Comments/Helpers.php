<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments\Model\Repository\CommentRepository;

class Helpers
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\Repository\CommentRepository
     */
    protected $commentRepository;

    /**
     * @param \ACP3\Core\Modules                                             $modules
     * @param \ACP3\Modules\ACP3\Comments\Model\Repository\CommentRepository $commentRepository
     */
    public function __construct(
        Core\Modules $modules,
        CommentRepository $commentRepository
    ) {
        $this->modules = $modules;
        $this->commentRepository = $commentRepository;
    }

    /**
     * Zählt die Anzahl der Kommentare für einen bestimmten Eintrag eines Modules zusammen.
     *
     * @param string $moduleName
     * @param int    $resultId
     *
     * @return int
     */
    public function commentsCount($moduleName, $resultId)
    {
        return $this->commentRepository->countAllByModule($this->modules->getModuleId($moduleName), $resultId);
    }

    /**
     * @param int $moduleId
     * @param int $resultId
     *
     * @return int
     */
    public function deleteCommentsByModuleAndResult($moduleId, $resultId)
    {
        return $this->commentRepository->delete(['module_id' => $moduleId, 'entry_id' => $resultId]);
    }
}
