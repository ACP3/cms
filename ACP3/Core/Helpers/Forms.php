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
     * @param string               $formFieldName
     * @param mixed                $defaultValue
     * @param string|integer|array $currentValue
     * @param string               $htmlAttribute
     *
     * @return string
     */
    public function selectEntry($formFieldName, $defaultValue, $currentValue = '', $htmlAttribute = '')
    {
        $htmlAttribute = $this->buildHtmlAttribute($htmlAttribute);
        $currentValue = $this->request->getPost()->get($formFieldName, $currentValue);

        if (is_array($currentValue) === false && $currentValue == $defaultValue) {
            return $htmlAttribute;
        } elseif (is_array($currentValue) === true && in_array($defaultValue, $currentValue)) {
            return $htmlAttribute;
        }

        return '';
    }

    /**
     * @param string $htmlAttribute
     *
     * @return string
     */
    private function buildHtmlAttribute($htmlAttribute)
    {
        if (empty($htmlAttribute)) {
            $htmlAttribute = 'selected';
        }

        return ' ' . $htmlAttribute . '="' . $htmlAttribute . '"';
    }

    /**
     *
     * @param string               $formFieldName
     * @param array                $values
     * @param string|integer|array $currentValue
     * @param string               $htmlAttribute
     *
     * @return array
     */
    public function choicesGenerator($formFieldName, array $values, $currentValue = '', $htmlAttribute = 'selected')
    {
        $select = [];
        $id = str_replace('_', '-', $formFieldName);
        foreach ($values as $value => $phrase) {
            $select[] = [
                'value' => $value,
                'id' => ($htmlAttribute === 'checked' ? $id . '-' . $value : ''),
                $htmlAttribute => $this->selectEntry($formFieldName, $value, $currentValue, $htmlAttribute),
                'lang' => $phrase
            ];
        }
        return $select;
    }

    /**
     * @param string $formFieldName
     * @param string $currentValue
     * @param string $htmlAttribute
     *
     * @return array
     */
    public function linkTargetChoicesGenerator($formFieldName, $currentValue = '', $htmlAttribute = 'selected')
    {
        $linkTargets = [
            1 => $this->translator->t('system', 'window_self'),
            2 => $this->translator->t('system', 'window_blank')
        ];

        return $this->choicesGenerator($formFieldName, $linkTargets, $currentValue, $htmlAttribute);
    }

    /**
     * @param string $formFieldName
     * @param string $currentValue
     * @param string $htmlAttribute
     *
     * @return array
     */
    public function yesNoChoicesGenerator($formFieldName, $currentValue = '', $htmlAttribute = 'selected')
    {
        $values = [
            1 => $this->translator->t('system', 'yes'),
            0 => $this->translator->t('system', 'no')
        ];

        return $this->choicesGenerator($formFieldName, $values, $currentValue, $htmlAttribute);
    }

    /**
     * @param string               $formFieldName
     * @param array                $values
     * @param string|integer|array $currentValue
     *
     * @return array
     */
    public function checkboxGenerator($formFieldName, array $values, $currentValue = '')
    {
        return $this->choicesGenerator($formFieldName, $values, $currentValue, 'checked');
    }

    /**
     * @param string $formFieldName
     * @param string $currentValue
     *
     * @return array
     */
    public function yesNoCheckboxGenerator($formFieldName, $currentValue = '')
    {
        $values = [
            1 => $this->translator->t('system', 'yes'),
            0 => $this->translator->t('system', 'no')
        ];

        return $this->checkboxGenerator($formFieldName, $values, $currentValue);
    }
}
