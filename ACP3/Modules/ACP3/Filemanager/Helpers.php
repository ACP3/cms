<?php
namespace ACP3\Modules\ACP3\Filemanager;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Filemanager
 */
class Helpers
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * Helpers constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(Core\Environment\ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * @return string
     */
    public function getFilemanagerPath()
    {
        return $this->appPath->getWebRoot() . 'ACP3/Modules/ACP3/Filemanager/libraries/kcfinder/';
    }
}
