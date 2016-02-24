<?php
namespace ACP3\Installer\Modules\Install\Controller;

use ACP3\Installer\Core;

/**
 * Class AbstractController
 * @package ACP3\Installer\Modules\Install\Controller
 */
abstract class AbstractAction extends Core\Controller\AbstractInstallerController
{
    public function preDispatch()
    {
        parent::preDispatch();

        $navbar = [
            'index_index' => [
                'lang' => $this->translator->t('install', 'index_index'),
                'active' => false,
            ],
            'index_licence' => [
                'lang' => $this->translator->t('install', 'index_licence'),
                'active' => false,
            ],
            'index_requirements' => [
                'lang' => $this->translator->t('install', 'index_requirements'),
                'active' => false,
            ],
            'install_index' => [
                'lang' => $this->translator->t('install', 'install_index'),
                'active' => false,
            ]
        ];

        $key = $this->request->getController() . '_' . $this->request->getControllerAction();
        if (isset($navbar[$key]) === true) {
            $navbar[$key]['active'] = true;
        }
        $this->view->assign('navbar', $navbar);
    }
}
