<?php
namespace ACP3\Modules\ACP3\Acp\Controller\Admin\Index;

use ACP3\Core;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Acp\Controller\Admin\Index
 */
class Index extends Core\Controller\AdminController
{
    /**
     * @return array
     */
    public function execute()
    {
        $activeModules = $this->modules->getActiveModules();
        $allowedModules = [];

        foreach ($activeModules as $name => $info) {
            $dir = strtolower($info['dir']);
            if ($this->acl->hasPermission('admin/' . $dir) === true && $dir !== 'acp') {
                $allowedModules[$name]['name'] = $name;
                $allowedModules[$name]['dir'] = $dir;
            }
        }

        return [
            'modules' => $allowedModules
        ];
    }
}
