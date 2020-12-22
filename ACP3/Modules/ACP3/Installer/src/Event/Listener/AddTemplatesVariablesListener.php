<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Event\Listener;

use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\ExtractFromPathTrait;
use ACP3\Core\I18n\Translator;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath;

class AddTemplatesVariablesListener
{
    use ExtractFromPathTrait;

    /**
     * @var \ACP3\Modules\ACP3\Installer\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Environment\ThemePathInterface
     */
    private $theme;

    public function __construct(
        ApplicationPath $appPath,
        ThemePathInterface $theme,
        Translator $translator,
        View $view,
        RequestInterface $request
    ) {
        $this->appPath = $appPath;
        $this->translator = $translator;
        $this->view = $view;
        $this->request = $request;
        $this->theme = $theme;
    }

    public function __invoke()
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
            'LAYOUT' => $this->request->isXmlHttpRequest() ? 'layout.ajax.tpl' : 'layout.tpl',
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
                $locale = \str_replace('-', '_', $locale);
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
     */
    private function languagesDropdown(string $selectedLanguage): array
    {
        return $this->translator->getLanguagePacks($selectedLanguage);
    }
}
