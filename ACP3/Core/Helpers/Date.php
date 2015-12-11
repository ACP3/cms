<?php
namespace ACP3\Core\Helpers;

use ACP3\Core\Http\Request;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\ValidationRules\DateValidationRule;

/**
 * Class Date
 * @package ACP3\Core\Helpers
 */
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
     * @var \ACP3\Core\Http\Request
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
     * @param \ACP3\Core\Http\Request                                  $request
     * @param \ACP3\Core\Helpers\Forms                                 $formsHelper
     * @param \ACP3\Core\Validation\ValidationRules\DateValidationRule $dateValidationRule
     */
    public function __construct(
        \ACP3\Core\Date $date,
        Translator $translator,
        Request $request,
        Forms $formsHelper,
        DateValidationRule $dateValidationRule
    )
    {
        $this->date = $date;
        $this->translator = $translator;
        $this->request = $request;
        $this->formsHelper = $formsHelper;
        $this->dateValidationRule = $dateValidationRule;
    }

    /**
     * Liefert ein Array mit allen Zeitzonen dieser Welt aus
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
            'Pacitic' => \DateTimeZone::listIdentifiers(\DateTimeZone::PACIFIC),
            'UTC' => \DateTimeZone::listIdentifiers(\DateTimeZone::UTC),
        ];

        foreach ($timeZones as $key => $values) {
            $i = 0;
            foreach ($values as $row) {
                unset($timeZones[$key][$i]);
                $timeZones[$key][$row]['selected'] = $this->formsHelper->selectEntry('date_time_zone', $row, $currentValue);
                ++$i;
            }
        }
        return $timeZones;
    }

    /**
     * Gibts ein Array mit den m�glichen Datumsformaten aus,
     * um diese als Dropdownmen� darstellen zu k�nnen
     *
     * @param string $format
     *    Optionaler Parameter f�r das aktuelle Datumsformat
     *
     * @return array
     */
    public function dateFormatDropdown($format = '')
    {
        $dateFormatLang = [
            $this->translator->t('system', 'date_format_short'),
            $this->translator->t('system', 'date_format_long')
        ];
        return $this->formsHelper->selectGenerator('dateformat', ['short', 'long'], $dateFormatLang, $format);
    }

    /**
     * Displays an input field with an associated datepicker
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
    )
    {
        $datePicker = [
            'range' => $this->isRange($name),
            'with_time' => (bool)$showTime,
            'length' => $showTime === true ? 16 : 10,
            'input_only' => (bool)$inputFieldOnly,
            'params' => [
                'format' => $this->getPickerDateFormat($showTime),
                'changeMonth' => 'true',
                'changeYear' => 'true',
            ]
        ];

        if ($this->isRange($name) === true) {
            $datePicker['name_start'] = $name[0];
            $datePicker['name_end'] = $name[1];
            $datePicker['id_start'] = $this->getInputId($name[0]);
            $datePicker['id_end'] = $this->getInputId($name[1]);

            $datePicker = array_merge($datePicker, $this->fetchRangeDatePickerValues($name, $value, $showTime));

            $datePicker['range_json'] = json_encode(
                [
                    'start' => '#' . $datePicker['id_start'],
                    'startDefaultDate' => $datePicker['value_start_r'],
                    'end' => '#' . $datePicker['id_end'],
                    'endDefaultDate' => $datePicker['value_end_r']
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
        return 'date-' . str_replace('_', '-', $fieldName);
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
            $valueStartR = $this->date->format($valueStart, 'r', false);
            $valueEndR = $this->date->format($valueEnd, 'r', false);
        } elseif (is_array($value) && $this->dateValidationRule->isValid($value) === true) {
            $valueStart = $this->date->format($value[0], $this->getDateFormat($showTime));
            $valueEnd = $this->date->format($value[1], $this->getDateFormat($showTime));
            $valueStartR = $this->date->format($value[0], 'r');
            $valueEndR = $this->date->format($value[1], 'r');
        } else {
            $valueStart = $this->date->format('now', $this->getDateFormat($showTime), false);
            $valueEnd = $this->date->format('now', $this->getDateFormat($showTime), false);
            $valueStartR = $this->date->format('now', 'r', false);
            $valueEndR = $this->date->format('now', 'r', false);
        }

        return [
            'value_start' => $valueStart,
            'value_end' => $valueEnd,
            'value_start_r' => $valueStartR,
            'value_end_r' => $valueEndR
        ];
    }

    /**
     * @param string $name
     * @param string $value
     * @param bool $showTime
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
        return (is_array($name) === true);
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