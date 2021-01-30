<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\EventListener;

use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\View;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddTemplateVariablesListener implements EventSubscriberInterface
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
            'DESIGN_PATH' => $this->theme->getDesignPathWeb(),
            'DESIGN_PATH_ABSOLUTE' => $this->request->getScheme() . '://' . $this->request->getHttpHost() . $this->theme->getDesignPathWeb(),
            'HOST_NAME' => $this->request->getHttpHost(),
            'IN_ADM' => $this->request->getArea() === AreaEnum::AREA_ADMIN,
            'IS_HOMEPAGE' => $this->request->isHomepage(),
            'IS_AJAX' => $this->request->isXmlHttpRequest(),
            'LANG_DIRECTION' => $this->translator->getDirection(),
            'LANG' => $this->translator->getShortIsoCode(),
            'PHP_SELF' => $this->appPath->getPhpSelf(),
            'REQUEST_URI' => $this->request->getServer()->get('REQUEST_URI'),
            'ROOT_DIR' => $this->appPath->getWebRoot(),
            'ROOT_DIR_ABSOLUTE' => $this->request->getScheme() . '://' . $this->request->getHttpHost() . $this->appPath->getWebRoot(),
            'UA_IS_MOBILE' => $this->request->getUserAgent()->isMobileBrowser(),
            'UPLOADS_DIR' => $this->appPath->getWebRoot() . 'uploads/',
        ]);

        if ($this->request->getArea() !== AreaEnum::AREA_WIDGET) {
            $this->fetchLayoutViaInheritance();
        }
    }

    protected function fetchLayoutViaInheritance(): void
    {
        if ($this->request->isXmlHttpRequest()) {
            $paths = $this->fetchLayoutPaths('layout.ajax', 'System/layout.ajax.tpl');
        } else {
            $paths = $this->fetchLayoutPaths('layout', 'layout.tpl');
        }

        $this->iterateOverLayoutPaths($paths);
    }

    private function fetchLayoutPaths(string $layoutFileName, string $defaultLayoutName): array
    {
        return [
            $this->request->getModule() . '/' . $this->request->getArea() . '/' . $layoutFileName . '.' . $this->request->getController() . '.' . $this->request->getAction() . '.tpl',
            $this->request->getModule() . '/' . $this->request->getArea() . '/' . $layoutFileName . '.' . $this->request->getController() . '.tpl',
            $this->request->getModule() . '/' . $this->request->getArea() . '/' . $layoutFileName . '.tpl',
            $this->request->getModule() . '/' . $layoutFileName . '.tpl',
            $defaultLayoutName,
        ];
    }

    /**
     * @param string[] $paths
     */
    private function iterateOverLayoutPaths(array $paths): void
    {
        foreach ($paths as $path) {
            if ($this->view->templateExists($path)) {
                $this->view->setLayout($path);

                break;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerActionBeforeDispatchEvent::NAME => '__invoke',
        ];
    }
}
