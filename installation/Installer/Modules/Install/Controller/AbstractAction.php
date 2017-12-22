<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Controller;

use ACP3\Installer\Core;
use ACP3\Installer\Modules\Install\Helpers\Navigation;

/**
 * Class AbstractController
 * @package ACP3\Installer\Modules\Install\Controller
 */
abstract class AbstractAction extends Core\Controller\AbstractInstallerAction
{
    /**
     * @var Navigation
     */
    protected $navigation;

    /**
     * AbstractAction constructor.
     * @param Core\Controller\Context\InstallerContext $context
     * @param Navigation $navigation
     */
    public function __construct(Core\Controller\Context\InstallerContext $context, Navigation $navigation)
    {
        parent::__construct($context);

        $this->navigation = $navigation;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $key = $this->request->getController() . '_' . $this->request->getAction();
        $completedSteps = 0;
        if ($this->navigation->has($key) === true) {
            $this->navigation->markStepActive($key);
            $completedSteps = array_search($key, array_keys($this->navigation->all()));
        }

        if ($completedSteps > 0) {
            $i = 0;
            foreach ($this->navigation->all() as $key => $value) {
                if ($i < $completedSteps) {
                    $this->navigation->markStepComplete($key);
                    ++$i;
                }
            }
        }
    }

    protected function addCustomTemplateVarsBeforeOutput()
    {
        parent::addCustomTemplateVarsBeforeOutput();

        $this->view->assign('navbar', $this->navigation->all());
    }
}
