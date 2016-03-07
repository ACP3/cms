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
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;

    /**
     * @param \ACP3\Core\I18n\Translator       $translator
     * @param \ACP3\Core\Http\RequestInterface $request
     */
    public function __construct(
        Translator $translator,
        RequestInterface $request
    ) {
        $this->translator = $translator;
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
     * @param string|integer|array $currentValue
     * @param string               $selected
     *
     * @return array
     */
    public function selectGenerator($name, array $values, $currentValue = '', $selected = 'selected')
    {
        $select = [];
        $id = str_replace('_', '-', $name);
        foreach ($values as $value => $phrase) {
            $select[] = [
                'value' => $value,
                'id' => ($selected === 'checked' ? $id . '-' . $value : ''),
                $selected => $this->selectEntry($name, $value, $currentValue, $selected),
                'lang' => $phrase
            ];
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
        $linkTargets = [
            1 => $this->translator->t('system', 'window_self'),
            2 => $this->translator->t('system', 'window_blank')
        ];

        return $this->selectGenerator($name, $linkTargets, $currentValue, $selected);
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
        $values = [
            1 => $this->translator->t('system', 'yes'),
            0 => $this->translator->t('system', 'no')
        ];

        return $this->selectGenerator($name, $values, $currentValue, $selected);
    }

    /**
     * @param string               $name
     * @param array                $values
     * @param string|integer|array $currentValue
     *
     * @return array
     */
    public function checkboxGenerator($name, array $values, $currentValue = '')
    {
        return $this->selectGenerator($name, $values, $currentValue, 'checked');
    }

    /**
     * @param string $name
     * @param string $currentValue
     *
     * @return array
     */
    public function yesNoCheckboxGenerator($name, $currentValue = '')
    {
        $values = [
            1 => $this->translator->t('system', 'yes'),
            0 => $this->translator->t('system', 'no')
        ];

        return $this->checkboxGenerator($name, $values, $currentValue);
    }
}
