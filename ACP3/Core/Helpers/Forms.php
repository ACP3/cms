<?php
namespace ACP3\Core\Helpers;

use ACP3\Core\Http\Request;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

/**
 * Class Forms
 * @package ACP3\Core\Helpers
 */
class Forms
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;

    /**
     * @param \ACP3\Core\I18n\Translator       $lang
     * @param \ACP3\Core\Http\RequestInterface $request
     */
    public function __construct(
        Translator $lang,
        RequestInterface $request
    )
    {
        $this->lang = $lang;
        $this->request = $request;
    }

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
     * @param string               $name
     * @param mixed                $value
     * @param string|integer|array $currentValue
     * @param string               $attr
     *
     * @return string
     */
    public function selectEntry($name, $value, $currentValue = '', $attr = 'selected')
    {
        $attr = ' ' . $attr . '="' . $attr . '"';
        $currentValue = $this->request->getPost()->get($name, $currentValue);

        if (is_array($currentValue) === false && $currentValue == $value) {
            return $attr;
        } elseif (is_array($currentValue) === true) {
            foreach ($currentValue as $row) {
                if ($row == $value) {
                    return $attr;
                }
            }
        }

        return '';
    }

    /**
     *
     * @param string               $name
     * @param array                $values
     * @param array                $lang
     * @param string|integer|array $currentValue
     * @param string               $selected
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

    /**
     * @param string $name
     * @param string $currentValue
     * @param string $selected
     *
     * @return array
     */
    public function linkTargetSelectGenerator($name, $currentValue = '', $selected = 'selected')
    {
        $langTarget = [$this->lang->t('system', 'window_self'), $this->lang->t('system', 'window_blank')];
        return $this->selectGenerator($name, [1, 2], $langTarget, $currentValue, $selected);
    }

    /**
     * @param string $name
     * @param string $currentValue
     * @param string $selected
     *
     * @return array
     */
    public function yesNoSelectGenerator($name, $currentValue = '', $selected = 'selected')
    {
        return $this->selectGenerator(
            $name,
            [1, 0],
            [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')],
            $currentValue,
            $selected
        );
    }

    /**
     * @param string               $name
     * @param array                $values
     * @param array                $lang
     * @param string|integer|array $currentValue
     *
     * @return array
     */
    public function checkboxGenerator($name, array $values, array $lang, $currentValue = '')
    {
        return $this->selectGenerator($name, $values, $lang, $currentValue, 'checked');
    }

    /**
     * @param string $name
     * @param string $currentValue
     *
     * @return array
     */
    public function yesNoCheckboxGenerator($name, $currentValue = '')
    {
        return $this->checkboxGenerator(
            $name,
            [1, 0],
            [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')],
            $currentValue
        );
    }
}
