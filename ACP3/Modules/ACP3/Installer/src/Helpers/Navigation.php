<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Helpers;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Installer\Helpers\Navigation\NavigationStep;

class Navigation
{
    /**
     * @var \ACP3\Modules\ACP3\Installer\Helpers\Navigation\NavigationStep[]
     */
    private array $navbar = [];

    public function __construct(private Translator $translator)
    {
        $this->initializeNavigation();
    }

    private function initializeNavigation(): void
    {
        $this
            ->addStep(
                'index_index',
                ['lang' => $this->translator->t('installer', 'installer_index_index')]
            )
            ->addStep(
                'index_licence',
                ['lang' => $this->translator->t('installer', 'installer_index_licence')]
            )
            ->addStep(
                'index_requirements',
                ['lang' => $this->translator->t('installer', 'installer_index_requirements')]
            )
            ->addStep(
                'index_install',
                ['lang' => $this->translator->t('installer', 'installer_index_install')]
            );
    }

    /**
     * @param array<string, mixed> $options
     */
    private function addStep(string $stepName, array $options): self
    {
        if (!$this->has($stepName)) {
            $options = array_merge($this->getDefaultOptions(), $options);
            $this->navbar[$stepName] = new NavigationStep($options['lang'], $options['active'], $options['complete']);
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    private function getDefaultOptions(): array
    {
        return [
            'lang' => '',
            'active' => false,
            'complete' => false,
        ];
    }

    public function markStepComplete(string $stepName): self
    {
        if ($this->has($stepName)) {
            $this->navbar[$stepName]->setIsComplete(true);
        }

        return $this;
    }

    public function has(string $stepName): bool
    {
        return \array_key_exists($stepName, $this->navbar);
    }

    public function markStepActive(string $stepName): self
    {
        if ($this->has($stepName)) {
            $this->navbar[$stepName]->setIsActive(true);
        }

        return $this;
    }

    /**
     * @return NavigationStep[]
     */
    public function all(): array
    {
        return $this->navbar;
    }

    public function setProgress(RequestInterface $request): void
    {
        $key = $request->getController() . '_' . $request->getAction();

        $completedSteps = 0;
        if ($this->has($key) === true) {
            $this->markStepActive($key);
            $completedSteps = array_search($key, array_keys($this->all()), true);
        }

        if ($completedSteps > 0) {
            $i = 0;
            foreach ($this->all() as $key => $value) {
                if ($i < $completedSteps) {
                    $this->markStepComplete($key);
                    ++$i;
                }
            }
        }
    }
}
