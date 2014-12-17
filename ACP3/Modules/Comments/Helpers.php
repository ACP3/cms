<?php
namespace ACP3\Modules\Comments;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\Comments
 */
class Helpers
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var Model
     */
    protected $comentsModel;

    /**
     * @param \ACP3\Core\Modules           $modules
     * @param \ACP3\Modules\Comments\Model $commentsModel
     */
    public function __construct(
        Core\Modules $modules,
        Model $commentsModel)
    {
        $this->modules = $modules;
        $this->comentsModel = $commentsModel;
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
        return $this->comentsModel->countAllByModule($this->modules->getModuleId($moduleName), $resultId);
    }

    /**
     * @param $moduleId
     * @param $resultId
     *
     * @return int
     */
    public function deleteCommentsByModuleAndResult($moduleId, $resultId)
    {
        return $this->comentsModel->delete(['module_id' => $moduleId, 'entry_id' => $resultId]);
    }
}
