<?php
namespace ACP3\Core\Validator\Rules;

/**
 * Class Date
 * @package ACP3\Core\Validator\Rules
 */
class Date
{
    /**
     * Überprüft einen Geburtstag auf seine Gültigkeit
     *
     * @param string $var
     *  Das zu überprüfende Datum
     *
     * @return boolean
     */
    public function birthday($var)
    {
        $regex = '/^(\d{4})-(\d{2})-(\d{2})$/';
        $matches = [];
        if (preg_match($regex, $var, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Überprüft, ob alle Daten ein sinnvolles Datum ergeben
     *
     * @param string $start
     *  Startdatum
     * @param string $end
     *  Enddatum
     *
     * @return boolean
     */
    public function date($start, $end = null)
    {
        $matchesStart = $matchesEnd = [];
        $regex = '/^(\d{4})-(\d{2})-(\d{2})( ([01][0-9]|2[0-3])(:([0-5][0-9])){1,2}){0,1}$/';
        if (preg_match($regex, $start, $matchesStart)) {
            // Wenn ein Enddatum festgelegt wurde, dieses ebenfalls mit überprüfen
            if ($end != null && preg_match($regex, $end, $matchesEnd)) {
                if (checkdate($matchesStart[2], $matchesStart[3], $matchesStart[1]) &&
                    checkdate($matchesEnd[2], $matchesEnd[3], $matchesEnd[1]) &&
                    strtotime($start) <= strtotime($end)
                ) {
                    return true;
                }
                // Nur Startdatum überprüfen
            } else {
                if (checkdate($matchesStart[2], $matchesStart[3], $matchesStart[1])) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Überprüft, ob eine gültige Zeitzone gewählt wurde
     *
     * @param string $var
     *    Die zu überprüfende Variable
     *
     * @return boolean
     */
    public function timeZone($var)
    {
        $bool = true;
        try {
            new \DateTimeZone($var);
        } catch (\Exception $e) {
            $bool = false;
        }
        return $bool;
    }


} 