<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\ValidationRules\DateValidationRule;

class Date
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Core\Validation\ValidationRules\DateValidationRule
     */
    protected $dateValidationRule;

    /**
     * Date constructor.
     *
     * @param \ACP3\Core\Date                                          $date
     * @param \ACP3\Core\I18n\Translator                               $translator
     * @param \ACP3\Core\Http\RequestInterface                         $request
     * @param \ACP3\Core\Helpers\Forms                                 $formsHelper
     * @param \ACP3\Core\Validation\ValidationRules\DateValidationRule $dateValidationRule
     */
    public function __construct(
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
    }

    /**
     * Liefert ein Array mit allen Zeitzonen dieser Welt aus.
     *
     * @param string $currentValue
     *
     * @return array
     */
    public function getTimeZones($currentValue = '')
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
     *
     * @param string $currentDateFormat
     *
     * @return array
     */
    public function dateFormatDropdown($currentDateFormat = '')
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
     * @param bool         $showTime
     * @param bool         $inputFieldOnly
     *
     * @return array
     */
    public function datepicker(
        $name,
        $value = '',
        $showTime = true,
        $inputFieldOnly = false
    ) {
        $datePicker = [
            'range' => $this->isRange($name),
            'length' => $showTime === true ? 16 : 10,
            'input_only' => (bool) $inputFieldOnly,
            'params' => \json_encode([
                'format' => $this->getPickerDateFormat($showTime),
                'locale' => $this->translator->getLocale(),
            ]),
        ];

        if ($this->isRange($name) === true) {
            $datePicker['name_start'] = $name[0];
            $datePicker['name_end'] = $name[1];
            $datePicker['id_start'] = $this->getInputId($name[0]);
            $datePicker['id_end'] = $this->getInputId($name[1]);

            $datePicker = \array_merge($datePicker, $this->fetchRangeDatePickerValues($name, $value, $showTime));

            $datePicker['range_json'] = \json_encode(
                [
                    [
                        'element' => '#' . $datePicker['id_start'],
                        'defaultDate' => $datePicker['value_start_r'],
                        'format' => $this->getPickerDateFormat($showTime),
                        'locale' => $this->translator->getLocale(),
                    ],
                    [
                        'element' => '#' . $datePicker['id_end'],
                        'defaultDate' => $datePicker['value_end_r'],
                        'format' => $this->getPickerDateFormat($showTime),
                        'minDate' => $datePicker['value_start_r'],
                        'locale' => $this->translator->getLocale(),
                    ],
                ]
            );
        } else { // Einfaches Inputfeld mit Datepicker
            $datePicker['name'] = $name;
            $datePicker['id'] = $this->getInputId($name);
            $datePicker['value'] = $this->fetchSimpleDatePickerValue($name, $value, $showTime);
        }

        return $datePicker;
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    protected function getInputId($fieldName)
    {
        return 'date-' . \str_replace('_', '-', $fieldName);
    }

    /**
     * @param array $name
     * @param array $value
     * @param bool  $showTime
     *
     * @return array
     */
    protected function fetchRangeDatePickerValues(array $name, $value, $showTime)
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

    /**
     * @param string $name
     * @param string $value
     * @param bool   $showTime
     *
     * @return string
     */
    protected function fetchSimpleDatePickerValue($name, $value, $showTime)
    {
        if ($this->request->getPost()->has($name)) {
            return $this->request->getPost()->get($name, '');
        } elseif ($this->dateValidationRule->isValid($value) === true) {
            return $this->date->format($value, $this->getDateFormat($showTime));
        }

        return $this->date->format('now', $this->getDateFormat($showTime), false);
    }

    /**
     * @param bool $showTime
     *
     * @return string
     */
    protected function getPickerDateFormat($showTime)
    {
        return 'YYYY-MM-DD' . ($showTime === true ? ' HH:mm' : '');
    }

    /**
     * @param string|array $name
     *
     * @return bool
     */
    protected function isRange($name)
    {
        return \is_array($name) === true;
    }

    /**
     * @param bool $showTime
     *
     * @return string
     */
    protected function getDateFormat($showTime)
    {
        return $showTime === true ? \ACP3\Core\Date::DEFAULT_DATE_FORMAT_LONG : \ACP3\Core\Date::DEFAULT_DATE_FORMAT_SHORT;
    }
}
