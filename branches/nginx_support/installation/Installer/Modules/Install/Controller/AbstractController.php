<?php
namespace ACP3\Installer\Modules\Install\Controller;

use ACP3\Installer\Core;

/**
 * Class AbstractController
 * @package ACP3\Installer\Modules\Install\Controller
 */
abstract class AbstractController extends Core\Modules\Controller
{
    public function preDispatch()
    {
        parent::preDispatch();

        $navbar = [
            'index_index' => [
                'lang' => $this->lang->t('install', 'index_index'),
                'active' => false,
            ],
            'index_licence' => [
                'lang' => $this->lang->t('install', 'index_licence'),
                'active' => false,
            ],
            'index_requirements' => [
                'lang' => $this->lang->t('install', 'index_requirements'),
                'active' => false,
            ],
            'install_index' => [
                'lang' => $this->lang->t('install', 'install_index'),
                'active' => false,
            ]
        ];

        $key = $this->request->controller . '_' . $this->request->file;
        if (isset($navbar[$key]) === true) {
            $navbar[$key]['active'] = true;
        }
        $this->view->assign('navbar', $navbar);
    }
}
