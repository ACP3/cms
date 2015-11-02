<?php
namespace ACP3\Core\Helpers\DataTable;


abstract class AbstractColumnRenderer
{
    const TYPE_TH = 'th';
    const TYPE_TD = 'td';

    /**
     * @param string $value
     * @param string $type
     * @param array  $attribute
     * @param string $class
     * @param string $style
     *
     * @return string
     */
    public function renderColumn($value, $type = self::TYPE_TD, $attribute = [], $class = '', $style = '')
    {
        $attribute = $this->addHtmlAttribute($attribute);
        $class = $this->addHtmlAttribute('class', $class);
        $style = $this->addHtmlAttribute('style', $style);

        return "<{$type}{$attribute}{$class}{$style}>{$value}</{$type}>";
    }

    /**
     * @param string|array $attributeName
     * @param string|null  $attributeData
     *
     * @return string
     */
    private function addHtmlAttribute($attributeName, $attributeData = null)
    {
        if (is_array($attributeName)) {
            $data = '';
            foreach ($attributeName as $key => $value) {
                $data .= $this->addHtmlAttribute($key, $value);
            }

            return $data;
        } elseif (!empty($attributeData)) {
            return ' ' . $attributeName . '="' . $attributeData . '"';
        }

        return '';
    }
}