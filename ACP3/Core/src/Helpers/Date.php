<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Validation\ValidationRules\DateValidationRule;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Date
{
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Validation\ValidationRules\DateValidationRule
     */
    private $dateValidationRule;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(
        SettingsInterface $settings,
        \ACP3\Core\Date $date,
        Translator $translator,
        RequestInterface $request,
        Forms $formsHelper,
        DateValidationRule $dateValidationRule
    ) {
        $this->date = $date;
        $this->translator = $translator;
        $this->request = $request;
        $this->formsHelper = $formsHelper;
        $this->dateValidationRule = $dateValidationRule;
        $this->settings = $settings;
    }

    /**
     * Liefert ein Array mit allen Zeitzonen dieser Welt aus.
     */
    public function getTimeZones(?string $currentValue = ''): array
    {
        $timeZones = [
            'Africa' => \DateTimeZone::listIdentifiers(\DateTimeZone::AFRICA),
            'America' => \DateTimeZone::listIdentifiers(\DateTimeZone::AMERICA),
            'Antarctica' => \DateTimeZone::listIdentifiers(\DateTimeZone::ANTARCTICA),
            'Arctic' => \DateTimeZone::listIdentifiers(\DateTimeZone::ARCTIC),
            'Asia' => \DateTimeZone::listIdentifiers(\DateTimeZone::ASIA),
            'Atlantic' => \DateTimeZone::listIdentifiers(\DateTimeZone::ATLANTIC),
            'Australia' => \DateTimeZone::listIdentifiers(\DateTimeZone::AUSTRALIA),
            'Europe' => \DateTimeZone::listIdentifiers(\DateTimeZone::EUROPE),
            'Indian' => \DateTimeZone::listIdentifiers(\DateTimeZone::INDIAN),
            'Pacific' => \DateTimeZone::listIdentifiers(\DateTimeZone::PACIFIC),
            'UTC' => \DateTimeZone::listIdentifiers(\DateTimeZone::UTC),
        ];

        foreach ($timeZones as $key => $values) {
            $i = 0;
            foreach ($values as $row) {
                unset($timeZones[$key][$i]);
                $timeZones[$key][$row]['selected'] = $this->formsHelper->selectEntry(
                    'date_time_zone',
                    $row,
                    $currentValue
                );
                ++$i;
            }
        }

        return $timeZones;
    }

    /**
     * Gibts ein Array mit den möglichen Datumsformaten aus,
     * um diese als Dropdownmenü darstellen zu können.
     */
    public function dateFormatDropdown(string $currentDateFormat = ''): array
    {
        $dateFormats = [
            'short' => $this->translator->t('system', 'date_format_short'),
            'long' => $this->translator->t('system', 'date_format_long'),
        ];

        return $this->formsHelper->choicesGenerator('dateformat', $dateFormats, $currentDateFormat);
    }

    /**
     * Displays an input field with an associated datepicker.
     *
     * @param string|array $name
     * @param string|array $value
     */
    public function datepicker(
        $name,
        $value = '',
        bool $showTime = true,
        bool $inputFieldOnly = false
    ): array {
        $datePicker = [
            'range' => $this->isRange($name),
            'with_time' => $showTime,
            'length' => $showTime === true ? 16 : 10,
            'input_only' => $inputFieldOnly,
            'config' => [
                'altFormat' => $this->getPickerDateFormat($showTime),
                'enableTime' => $showTime,
            ],
        ];

        if ($this->isRange($name) === true) {
            $datePicker['name_start'] = $name[0];
            $datePicker['name_end'] = $name[1];
            $datePicker['id_start'] = $this->getInputId($name[0]);
            $datePicker['id_end'] = $this->getInputId($name[1]);

            $datePicker = \array_merge($datePicker, $this->fetchRangeDatePickerValues($name, $value, $showTime));

            $datePicker['config'] = \array_merge(
                $datePicker['config'],
                [
                    'start' => '#' . $datePicker['id_start'],
                    'startDefaultDate' => $datePicker['value_start_r'],
                    'end' => '#' . $datePicker['id_end'],
                    'endDefaultDate' => $datePicker['value_end_r'],
                ]
            );
        } else { // Einfaches Inputfeld mit Datepicker
            $datePicker['name'] = $name;
            $datePicker['id'] = $this->getInputId($name);
            $datePicker['value'] = $this->fetchSimpleDatePickerValue($name, $value, $showTime);
            $datePicker['config'] = \array_merge(
                $datePicker['config'],
                [
                    'element' => '#' . $datePicker['id'],
                ]
            );
        }

        return $datePicker;
    }

    private function getInputId(string $fieldName): string
    {
        return 'date-' . \str_replace('_', '-', $fieldName);
    }

    private function fetchRangeDatePickerValues(array $name, array $value, bool $showTime): array
    {
        if ($this->request->getPost()->has($name[0]) && $this->request->getPost()->has($name[1])) {
            $valueStart = $this->request->getPost()->get($name[0]);
            $valueEnd = $this->request->getPost()->get($name[1]);
            $valueStartR = $this->date->format($valueStart, 'c', false);
            $valueEndR = $this->date->format($valueEnd, 'c', false);
        } elseif (\is_array($value) && $this->dateValidationRule->isValid($value) === true) {
            $valueStart = $this->date->format($value[0], $this->getDateFormat($showTime));
            $valueEnd = $this->date->format($value[1], $this->getDateFormat($showTime));
            $valueStartR = $this->date->format($value[0], 'c');
            $valueEndR = $this->date->format($value[1], 'c');
        } else {
            $valueStart = $this->date->format('now', $this->getDateFormat($showTime), false);
            $valueEnd = $this->date->format('now', $this->getDateFormat($showTime), false);
            $valueStartR = $this->date->format('now', 'c', false);
            $valueEndR = $this->date->format('now', 'c', false);
        }

        return [
            'value_start' => $valueStart,
            'value_end' => $valueEnd,
            'value_start_r' => $valueStartR,
            'value_end_r' => $valueEndR,
        ];
    }

    private function fetchSimpleDatePickerValue(string $name, string $value, bool $showTime): string
    {
        if ($this->request->getPost()->has($name)) {
            return $this->request->getPost()->get($name, '');
        }
        if ($this->dateValidationRule->isValid($value) === true) {
            return $this->date->format($value, $this->getDateFormat($showTime));
        }

        return $this->date->format('now', $this->getDateFormat($showTime), false);
    }

    private function getPickerDateFormat(bool $showTime): string
    {
        return $this->settings->getSettings(Schema::MODULE_NAME)[$showTime ? 'date_format_long' : 'date_format_short'];
    }

    /**
     * @param string|array $name
     */
    private function isRange($name): bool
    {
        return \is_array($name) === true;
    }

    private function getDateFormat(bool $showTime): string
    {
        return $showTime === true ? \ACP3\Core\Date::DEFAULT_DATE_FORMAT_LONG : \ACP3\Core\Date::DEFAULT_DATE_FORMAT_SHORT;
    }
}
