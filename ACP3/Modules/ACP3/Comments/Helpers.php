<?php
namespace ACP3\Modules\ACP3\Comments;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments\Model\Repository\CommentsRepository;

class Helpers
{
    /**
     * @var \ACP3\Core\Modules\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\Repository\CommentsRepository
     */
    protected $commentRepository;

    /**
     * @param \ACP3\Core\Modules\Modules                                  $modules
     * @param \ACP3\Modules\ACP3\Comments\Model\Repository\CommentsRepository $commentRepository
     */
    public function __construct(
        Core\Modules\Modules $modules,
        CommentsRepository $commentRepository
    ) {
        $this->modules = $modules;
        $this->commentRepository = $commentRepository;
    }

    /**
     * Zählt die Anzahl der Kommentare für einen bestimmten Eintrag eines Modules zusammen
     *
     * @param string  $moduleName
     * @param integer $resultId
     *
     * @return integer
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
