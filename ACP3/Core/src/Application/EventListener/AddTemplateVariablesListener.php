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
    public function __construct(private ApplicationPath $appPath, private RequestInterface $request, private ThemePathInterface $theme, private View $view, private Translator $translator)
    {
    }

    public function __invoke(): void
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
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerActionBeforeDispatchEvent::class => '__invoke',
        ];
    }
}
