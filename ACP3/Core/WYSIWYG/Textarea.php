<?php

namespace ACP3\Core\WYSIWYG;

use ACP3\Core;

/**
 * Implementation of the AbstractWYSIWYG class for a simple textarea
 * @package ACP3\Core\WYSIWYG
 */
class Textarea extends AbstractWYSIWYG
{
    /**
     * @inheritdoc
     */
    public function getFriendlyName()
    {
        return 'Textarea';
    }

    /**
     * @inheritdoc
     */
    public function setParameters(array $params = [])
    {
        $this->id = $params['id'];
        $this->name = $params['name'];
        $this->value = $params['value'];
        $this->advanced = isset($params['advanced']) ? (bool)$params['advanced'] : false;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return [
            'wysiwyg' => [
                'friendly_name' => $this->getFriendlyName(),
                'id' => $this->id,
                'name' => $this->name,
                'value' => $this->value,
                'js' => '',
                'advanced' => false
            ]
        ];
    }
}
