<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event\Listener;

use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\View;

class AddTemplateVariablesListener
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Environment\ThemePathInterface
     */
    private $theme;

    public function __construct(
        ApplicationPath $appPath,
        RequestInterface $request,
        ThemePathInterface $theme,
        View $view,
        Translator $translator
    ) {
        $this->appPath = $appPath;
        $this->request = $request;
        $this->view = $view;
        $this->translator = $translator;
        $this->theme = $theme;
    }

    public function __invoke(ControllerActionBeforeDispatchEvent $event)
    {
        $this->view->assign([
            'PHP_SELF' => $this->appPath->getPhpSelf(),
            'ROOT_DIR' => $this->appPath->getWebRoot(),
            'HOST_NAME' => $this->request->getHttpHost(),
            'ROOT_DIR_ABSOLUTE' => $this->request->getScheme() . '://' . $this->request->getHttpHost() . $this->appPath->getWebRoot(),
            'DESIGN_PATH' => $this->theme->getDesignPathWeb(),
            'DESIGN_PATH_ABSOLUTE' => $this->request->getScheme() . '://' . $this->request->getHttpHost() . $this->theme->getDesignPathWeb(),
            'LANG_DIRECTION' => $this->translator->getDirection(),
            'LANG' => $this->translator->getShortIsoCode(),
        ]);
    }
}
