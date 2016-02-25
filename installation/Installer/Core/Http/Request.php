<?php
namespace ACP3\Installer\Core\Http;

use ACP3\Core;

/**
 * Class Request
 * @package ACP3\Installer\Core\Http
 */
class Request extends Core\Http\Request
{
    /**
     * @inheritdoc
     */
    public function processQuery()
    {
        parent::processQuery();

        $this->area = Core\Controller\AreaEnum::AREA_INSTALL;
    }
}
