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
     * @var Model
     */
    protected $comentsModel;

    /**
     * @param Model $commentsModel
     */
    public function __construct(Model $commentsModel)
    {
        $this->comentsModel = $commentsModel;
    }

    /**
     * Zählt die Anzahl der Kommentare für einen bestimmten Eintrag eines Modules zusammen
     *
     * @param string  $moduleId
     *    Das jeweilige Modul
     * @param integer $resultId
     *    Die ID des jeweiligen Eintrages
     *
     * @return integer
     */
    public function commentsCount($moduleId, $resultId)
    {
        return $this->comentsModel->countAllByModule($moduleId, $resultId);
    }

    /**
     * @param $moduleName
     * @param $resultId
     *
     * @return int
     */
    public function deleteCommentsByModuleAndResult($moduleName, $resultId)
    {
        return $this->comentsModel->delete(['module_id' => $moduleName, 'entry_id' => $resultId]);
    }

}
