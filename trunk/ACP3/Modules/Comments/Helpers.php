<?php

/**
 * Comments
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

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
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Zählt die Anzahl der Kommentare für einen bestimmten Eintrag eines Modules zusammen
     *
     * @param string $moduleId
     *    Das jeweilige Modul
     * @param integer $resultId
     *    Die ID des jeweiligen Eintrages
     * @return integer
     */
    public function commentsCount($moduleId, $resultId)
    {
        return $this->model->countAllByModule($moduleId, $resultId);
    }

    /**
     * @param $moduleName
     * @param $resultId
     * @return int
     */
    public function deleteCommentsByModuleAndResult($moduleName, $resultId)
    {
        return $this->model->delete(array('module_id' => $moduleName, 'entry_id' => $resultId));
    }

}
