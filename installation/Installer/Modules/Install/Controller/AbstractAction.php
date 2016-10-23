<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Controller;

use ACP3\Installer\Core;

/**
 * Class AbstractController
 * @package ACP3\Installer\Modules\Install\Controller
 */
abstract class AbstractAction extends Core\Controller\AbstractInstallerAction
{
    protected $navbar = [];

    public function preDispatch()
    {
        parent::preDispatch();

        $this->navbar = [
            'index_index' => [
                'lang' => $this->translator->t('install', 'index_index'),
                'active' => false,
                'complete' => false,
            ],
            'index_licence' => [
                'lang' => $this->translator->t('install', 'index_licence'),
                'active' => false,
                'complete' => false,
            ],
            'index_requirements' => [
                'lang' => $this->translator->t('install', 'index_requirements'),
                'active' => false,
                'complete' => false,
            ],
            'install_index' => [
                'lang' => $this->translator->t('install', 'install_index'),
                'active' => false,
                'complete' => false,
            ]
        ];

        $key = $this->request->getController() . '_' . $this->request->getAction();
        $completedSteps = 0;
        if (isset($this->navbar[$key]) === true) {
            $this->navbar[$key]['active'] = true;
            $completedSteps = array_search($key, array_keys($this->navbar));
        }

        if ($completedSteps > 0) {
            $i = 0;
            foreach ($this->navbar as $key => $value) {
                if ($i < $completedSteps) {
                    $this->navbar[$key]['complete'] = true;
                    ++$i;
                }
            }
        }
    }

    protected function addCustomTemplateVarsBeforeOutput()
    {
        parent::addCustomTemplateVarsBeforeOutput();

        $this->view->assign('navbar', $this->navbar);
    }
}
