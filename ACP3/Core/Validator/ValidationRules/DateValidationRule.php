<?php
namespace ACP3\Core\Validator\ValidationRules;

/**
 * Class DateValidationRule
 * @package ACP3\Core\Validator\ValidationRules
 */
class DateValidationRule extends AbstractValidationRule
{
    const NAME = 'date';

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data)) {
            if (is_array($field)) {
                $start = reset($field);
                $end = next($field);

                return $this->checkIsValidDate($data[$start], $data[$end]);
            }

            return $this->checkIsValidDate($data[$field]);
        }

        return $this->checkIsValidDate($data);
    }

    /**
     * @param string      $start
     * @param string|null $end
     *
     * @return bool
     */
    protected function checkIsValidDate($start, $end = null)
    {
        if ($this->matchIsDate($start)) {
            // Check date range
            if ($end !== null && $this->matchIsDate($end) && strtotime($start) <= strtotime($end)) {
                return true;
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $date
     *
     * @return bool
     */
    protected function matchIsDate($date)
    {
        $pattern = '/^(\d{4})-(\d{2})-(\d{2})( ([01][0-9]|2[0-3])(:([0-5][0-9])){1,2}){0,1}$/';
        if (preg_match($pattern, $date, $matches) &&
            checkdate($matches[2], $matches[3], $matches[1])
        ) {
            return true;
        }

        return false;
    }
}