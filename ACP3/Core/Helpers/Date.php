<?php
namespace ACP3\Core\Helpers;

use ACP3\Core\Http\Request;
use ACP3\Core\Lang;

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
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Http\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Core\Validator\Rules\Date
     */
    protected $dateValidator;

    /**
     * @param \ACP3\Core\Date                 $date
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Http\Request         $request
     * @param \ACP3\Core\Helpers\Forms        $formsHelper
     * @param \ACP3\Core\Validator\Rules\Date $dateValidator
     */
    public function __construct(
        \ACP3\Core\Date $date,
        Lang $lang,
        Request $request,
        Forms $formsHelper,
        \ACP3\Core\Validator\Rules\Date $dateValidator
    )
    {
        $this->date = $date;
        $this->lang = $lang;
        $this->request = $request;
        $this->formsHelper = $formsHelper;
        $this->dateValidator = $dateValidator;
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
     * Gibts ein Array mit den möglichen Datumsformaten aus,
     * um diese als Dropdownmenü darstellen zu können
     *
     * @param string $format
     *    Optionaler Parameter für das aktuelle Datumsformat
     *
     * @return array
     */
    public function dateFormatDropdown($format = '')
    {
        $dateFormatLang = [
            $this->lang->t('system', 'date_format_short'),
            $this->lang->t('system', 'date_format_long')
        ];
        return $this->formsHelper->selectGenerator('dateformat', ['short', 'long'], $dateFormatLang, $format);
    }

    /**
     * Zeigt Dropdown-Menüs für die Veröffentlichungsdauer von Inhalten an
     *
     * @param mixed    $name
     *    Name des jeweiligen Inputfeldes
     * @param mixed    $value
     *    Der Zeitstempel des jeweiligen Eintrages
     * @param string   $format
     *    Das anzuzeigende Format im Textfeld
     * @param array    $params
     *    Dient dem Festlegen von weiteren Parametern
     * @param integer  $isRange
     *    1 = Start- und Enddatum anzeigen
     *    2 = Einfaches Inputfeld mitsamt Datepicker anzeigen
     * @param bool|int $withTime
     * @param bool     $inputFieldOnly
     *
     * @return array
     */
    public function datepicker(
        $name,
        $value = '',
        $format = 'Y-m-d H:i',
        array $params = [],
        $isRange = 1,
        $withTime = true,
        $inputFieldOnly = false
    )
    {
        $isRange = (is_array($name) === true && $isRange === 1);

        $datePicker = [
            'range' => $isRange,
            'with_time' => (bool)$withTime,
            'length' => $withTime === true ? 16 : 10,
            'input_only' => (bool)$inputFieldOnly,
            'params' => [
                'format' => 'YYYY-MM-DD',
                'changeMonth' => 'true',
                'changeYear' => 'true',
            ]
        ];
        if ($withTime === true) {
            $datePicker['params']['format'] .= ' HH:mm';
        }

        // Zusätzliche Datepicker-Parameter hinzufügen
        if (!empty($params) && is_array($params) === true) {
            $datePicker['params'] = array_merge($datePicker['params'], $params);
        }

        // Veröffentlichungszeitraum
        if ($isRange === true) {
            $datePicker['name_start'] = $name[0];
            $datePicker['name_end'] = $name[1];
            $datePicker['id_start'] = 'date-' . str_replace('_', '-', $name[0]);
            $datePicker['id_end'] = 'date-' . str_replace('_', '-', $name[1]);

            $datePicker = array_merge($datePicker, $this->fetchRangeDatePickerValues($name, $value, $format));

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
            $datePicker['id'] = 'date-' . str_replace('_', '-', $name);
            $datePicker['value'] = $this->fetchSimpleDatePickerValue($name, $value, $format);
        }

        return $datePicker;
    }

    /**
     * @param array  $name
     * @param array  $value
     * @param string $format
     *
     * @return array
     */
    protected function fetchRangeDatePickerValues($name, $value, $format)
    {
        if ($this->request->getPost()->has($name[0]) && $this->request->getPost()->has($name[1])) {
            $valueStart = $this->request->getPost()->get($name[0]);
            $valueEnd = $this->request->getPost()->get($name[1]);
            $valueStartR = $this->date->format($valueStart, 'r', false);
            $valueEndR = $this->date->format($valueEnd, 'r', false);
        } elseif (is_array($value) === true && $this->dateValidator->date($value[0], $value[1]) === true) {
            $valueStart = $this->date->format($value[0], $format);
            $valueEnd = $this->date->format($value[1], $format);
            $valueStartR = $this->date->format($value[0], 'r');
            $valueEndR = $this->date->format($value[1], 'r');
        } else {
            $valueStart = $this->date->format('now', $format, false);
            $valueEnd = $this->date->format('now', $format, false);
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
     * @param string $format
     *
     * @return string
     */
    protected function fetchSimpleDatePickerValue($name, $value, $format)
    {
        if ($this->request->getPost()->has($name)) {
            return $this->request->getPost()->get($name, '');
        } elseif ($this->dateValidator->date($value) === true) {
            return $this->date->format($value, $format);
        }

        return $this->date->format('now', $format, false);
    }

}