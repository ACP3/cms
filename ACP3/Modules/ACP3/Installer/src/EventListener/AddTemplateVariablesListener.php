<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\EventListener;

use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddTemplateVariablesListener implements EventSubscriberInterface
{
    public function __construct(private ApplicationPath $appPath, private Forms $formsHelper, private ThemePathInterface $theme, private Translator $translator, private View $view, private RequestInterface $request)
    {
    }

    public function __invoke(): void
    {
        $this->setLanguage();

        $this->view->assign([
            'LANGUAGES' => $this->languagesDropdown($this->translator->getLocale()),
            'PHP_SELF' => $this->appPath->getPhpSelf(),
            'REQUEST_URI' => $this->request->getServer()->get('REQUEST_URI'),
            'ROOT_DIR' => $this->appPath->getWebRoot(),
            'INSTALLER_ROOT_DIR' => $this->appPath->getInstallerWebRoot(),
            'DESIGN_PATH' => $this->theme->getDesignPathWeb(),
            'UA_IS_MOBILE' => $this->request->getUserAgent()->isMobileBrowser(),
            'IS_AJAX' => $this->request->isXmlHttpRequest(),
            'LANG_DIRECTION' => $this->translator->getDirection(),
            'LANG' => $this->translator->getShortIsoCode(),
        ]);
    }

    private function setLanguage(): void
    {
        $cookieLocale = $this->request->getCookies()->get('ACP3_INSTALLER_LANG', '');
        if ($this->translator->languagePackExists($cookieLocale)) {
            $language = $cookieLocale;
        } else {
            $language = 'en_US'; // Fallback language

            foreach ($this->request->getUserAgent()->parseAcceptLanguage() as $locale => $val) {
                $locale = str_replace('-', '_', $locale);
                if ($this->translator->languagePackExists($locale) === true) {
                    $language = $locale;

                    break;
                }
            }
        }

        $this->translator->setLocale($language);
    }

    /**
     * Generiert das Dropdown-Menü mit den zur Verfügung stehenden Installersprachen.
     *
     * @return array<string, mixed>[]
     */
    private function languagesDropdown(string $selectedLanguage): array
    {
        $languages = [];
        foreach ($this->translator->getLanguagePacks() as $languagePack) {
            $languages[$languagePack['iso']] = $languagePack['name'];
        }

        return $this->formsHelper->choicesGenerator('languages', $languages, $selectedLanguage);
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
