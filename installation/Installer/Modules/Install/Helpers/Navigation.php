<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Modules\Install\Helpers;

use ACP3\Core\I18n\TranslatorInterface;

class Navigation
{
    /**
     * @var array
     */
    private $navbar = [];
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Navigation constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        $this->initializeNavigation();
    }

    private function initializeNavigation()
    {
        $this
            ->addStep(
                'index_index',
                ['lang' => $this->translator->t('install', 'index_index')]
            )
            ->addStep(
                'index_licence',
                ['lang' => $this->translator->t('install', 'index_licence')]
            )
            ->addStep(
                'index_requirements',
                ['lang' => $this->translator->t('install', 'index_requirements')]
            )
            ->addStep(
                'install_index',
                ['lang' => $this->translator->t('install', 'install_index')]
            );
    }

    /**
     * @param string $stepName
     * @param array  $options
     *
     * @return $this
     */
    public function addStep($stepName, array $options)
    {
        if (!$this->has($stepName)) {
            $this->navbar[$stepName] = \array_merge($this->getDefaultOptions(), $options);
        }

        return $this;
    }

    /**
     * @return array
     */
    private function getDefaultOptions()
    {
        return [
            'lang' => '',
            'active' => false,
            'complete' => false,
        ];
    }

    /**
     * @param string $stepName
     *
     * @return $this
     */
    public function markStepComplete($stepName)
    {
        if ($this->has($stepName)) {
            $this->navbar[$stepName]['complete'] = true;
        }

        return $this;
    }

    /**
     * @param string $stepName
     *
     * @return bool
     */
    public function has($stepName)
    {
        return isset($this->navbar[$stepName]);
    }

    /**
     * @param string $stepName
     *
     * @return $this
     */
    public function markStepActive($stepName)
    {
        if ($this->has($stepName)) {
            $this->navbar[$stepName]['active'] = true;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->navbar;
    }
}
