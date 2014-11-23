<?php
namespace ACP3\Core\Helpers;

/**
 * Class Forms
 * @package ACP3\Core\Helpers
 */
class Forms
{
    /**
     * Liefert ein Array zur Ausgabe als Dropdown-Menü
     * für die Anzahl der anzuzeigenden Datensätze je Seite
     *
     * @param integer $currentValue
     * @param integer $steps
     * @param integer $maxValue
     *
     * @return array
     */
    public function recordsPerPage($currentValue, $steps = 5, $maxValue = 50)
    {
        $records = [];
        for ($i = 0, $j = $steps; $j <= $maxValue; $i++, $j += $steps) {
            $records[$i]['value'] = $j;
            $records[$i]['selected'] = $this->selectEntry('entries', $j, $currentValue);
        }
        return $records;
    }

    /**
     * Selektion eines Eintrages in einem Dropdown-Menü
     *
     * @param string $name
     *  Name des Feldes im Formular
     * @param mixed $defValue
     *  Abzugleichender Parameter mit $currentValue
     * @param mixed $currentValue
     *  Wert aus der SQL Tabelle
     * @param string $attr
     *  HTML-Attribut, um Eintrag zu selektieren
     *
     * @return string
     */
    public function selectEntry($name, $defValue, $currentValue = '', $attr = 'selected')
    {
        $attr = ' ' . $attr . '="' . $attr . '"';

        if (isset($_POST[$name]) === true) {
            $currentValue = $_POST[$name];
        }

        if (is_array($currentValue) === false && $currentValue == $defValue) {
            return $attr;
        } elseif (is_array($currentValue) === true) {
            foreach ($currentValue as $row) {
                if ($row == $defValue) {
                    return $attr;
                }
            }
        }

        return '';
    }

    /**
     *
     * @param string $name
     * @param array $values
     * @param array $lang
     * @param string|integer $currentValue
     * @param string $selected
     *
     * @return array
     */
    public function selectGenerator($name, array $values, array $lang, $currentValue = '', $selected = 'selected')
    {
        $select = [];
        if (count($values) == count($lang)) {
            $c_values = count($values);
            $id = str_replace('_', '-', $name);
            for ($i = 0; $i < $c_values; ++$i) {
                $select[] = [
                    'value' => $values[$i],
                    'id' => ($selected === 'checked' ? $id . '-' . $values[$i] : ''),
                    $selected => $this->selectEntry($name, $values[$i], $currentValue, $selected),
                    'lang' => $lang[$i]
                ];
            }
        }
        return $select;
    }

} 