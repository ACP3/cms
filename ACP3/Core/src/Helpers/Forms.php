<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

class Forms
{
    public function __construct(private Translator $translator, private RequestInterface $request)
    {
    }

    /**
     * Liefert ein Array zur Ausgabe als Dropdown-Menü
     * für die Anzahl der anzuzeigenden Datensätze je Seite.
     */
    public function recordsPerPage(
        ?int $currentValue,
        int $steps = 5,
        int $maxValue = 50,
        string $formFieldName = 'entries'
    ): array {
        $values = [];
        for ($i = $steps; $i <= $maxValue; $i += $steps) {
            $values[$i] = $i;
        }

        return $this->choicesGenerator($formFieldName, $values, $currentValue);
    }

    /**
     * Selektion eines Eintrages in einem Dropdown-Menü
     */
    public function selectEntry(
        string $formFieldName,
        mixed $defaultValue,
        array|int|string|null $currentValue = '',
        string $htmlAttribute = ''
    ): string {
        $htmlAttribute = $this->buildHtmlAttribute($htmlAttribute);
        $currentValue = $this->request->getPost()->get($formFieldName, $currentValue);

        if (\is_array($currentValue) === false && $currentValue == $defaultValue) {
            return $htmlAttribute;
        }
        if (\is_array($currentValue) === true && \in_array($defaultValue, $currentValue)) {
            return $htmlAttribute;
        }

        return '';
    }

    private function buildHtmlAttribute(string $htmlAttribute): string
    {
        if (empty($htmlAttribute)) {
            $htmlAttribute = 'selected';
        }

        return ' ' . $htmlAttribute . '="' . $htmlAttribute . '"';
    }

    /**
     * @return array<string, mixed>[]
     */
    public function choicesGenerator(
        string $formFieldName,
        array $values,
        array|int|string|null $currentValue = '',
        string $htmlAttribute = 'selected'
    ): array {
        $choices = [];
        $id = str_replace('_', '-', $formFieldName);
        foreach ($values as $value => $phrase) {
            $choices[] = [
                'value' => $value,
                'id' => ($htmlAttribute === 'checked' ? $id . '-' . $value : $id),
                'name' => $formFieldName,
                $htmlAttribute => $this->selectEntry($formFieldName, $value, $currentValue, $htmlAttribute),
                'lang' => $phrase,
            ];
        }

        return $choices;
    }

    /**
     * @return array<string, mixed>[]
     */
    public function linkTargetChoicesGenerator(
        string $formFieldName,
        ?int $currentValue = null,
        string $htmlAttribute = 'selected'
    ): array {
        $linkTargets = [
            1 => $this->translator->t('system', 'window_self'),
            2 => $this->translator->t('system', 'window_blank'),
        ];

        return $this->choicesGenerator($formFieldName, $linkTargets, $currentValue, $htmlAttribute);
    }

    /**
     * @return array<string, mixed>[]
     */
    public function yesNoChoicesGenerator(
        string $formFieldName,
        ?int $currentValue = null,
        string $htmlAttribute = 'selected'
    ): array {
        $values = [
            1 => $this->translator->t('system', 'yes'),
            0 => $this->translator->t('system', 'no'),
        ];

        return $this->choicesGenerator($formFieldName, $values, $currentValue, $htmlAttribute);
    }

    /**
     * @return array<string, mixed>[]
     */
    public function checkboxGenerator(string $formFieldName, array $values, array|int|string|null $currentValue = ''): array
    {
        return $this->choicesGenerator($formFieldName, $values, $currentValue, 'checked');
    }

    /**
     * @return array<string, mixed>[]
     */
    public function yesNoCheckboxGenerator(string $formFieldName, ?int $currentValue = null): array
    {
        $values = [
            1 => $this->translator->t('system', 'yes'),
            0 => $this->translator->t('system', 'no'),
        ];

        return $this->checkboxGenerator($formFieldName, $values, $currentValue);
    }
}
