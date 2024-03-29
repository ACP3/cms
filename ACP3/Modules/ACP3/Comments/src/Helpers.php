<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments\Repository\CommentRepository;

class Helpers
{
    public function __construct(protected Core\Modules $modules, protected CommentRepository $commentRepository)
    {
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
