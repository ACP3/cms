<?php
namespace ACP3\Core\Validator\Rules;

use ACP3\Core\Validator\ValidationRules\DateValidationRule;
use ACP3\Core\Validator\ValidationRules\TimeZoneExistsValidationRule;
use ACP3\Core\Validator\ValidationRules\BirthdayValidationRule;

/**
 * Class Date
 * @package ACP3\Core\Validator\Rules
 *
 * @deprecated
 */
class Date
{
    /**
     * @var \ACP3\Core\Validator\ValidationRules\DateValidationRule
     */
    protected $dateValidationRule;
    /**
     * @var \ACP3\Core\Validator\ValidationRules\TimeZoneExistsValidationRule
     */
    protected $timeZoneExistsValidationRule;
    /**
     * @var \ACP3\Core\Validator\ValidationRules\BirthdayValidationRule
     */
    protected $birthdayValidationRule;

    /**
     * Date constructor.
     *
     * @param \ACP3\Core\Validator\ValidationRules\DateValidationRule           $dateValidationRule
     * @param \ACP3\Core\Validator\ValidationRules\TimeZoneExistsValidationRule $timeZoneExistsValidationRule
     * @param \ACP3\Core\Validator\ValidationRules\BirthdayValidationRule       $birthdayValidationRule
     */
    public function __construct(
        DateValidationRule $dateValidationRule,
        TimeZoneExistsValidationRule $timeZoneExistsValidationRule,
        BirthdayValidationRule $birthdayValidationRule)
    {
        $this->dateValidationRule = $dateValidationRule;
        $this->timeZoneExistsValidationRule = $timeZoneExistsValidationRule;
        $this->birthdayValidationRule = $birthdayValidationRule;
    }

    /**
     * Überprüft einen Geburtstag auf seine Gültigkeit
     *
     * @param string $var
     *  Das zu überprüfende Datum
     *
     * @return boolean
     *
     * @deprecated
     */
    public function birthday($var)
    {
        return $this->birthdayValidationRule->isValid($var);
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
     *
     * @deprecated
     */
    public function date($start, $end = null)
    {
        $data = [
            'start' => $start,
            'end' => $end
        ];
        return $this->dateValidationRule->isValid($data, ['start', 'end']);
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
        return $this->timeZoneExistsValidationRule->isValid($var);
    }
}
