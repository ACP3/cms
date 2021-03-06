<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\WYSIWYG\Editor;

/**
 * Implementation of the AbstractWYSIWYG class for a simple textarea.
 */
class Textarea extends AbstractWYSIWYG
{
    /**
     * {@inheritdoc}
     */
    public function getFriendlyName()
    {
        return 'Textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $params = [])
    {
        $this->id = $params['id'];
        $this->name = $params['name'];
        $this->value = $params['value'];
        $this->advanced = isset($params['advanced']) ? (bool) $params['advanced'] : false;
        $this->required = isset($params['required']) ? (bool) $params['required'] : false;
    }

    /**
     * {@inheritdoc}
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
                'advanced' => false,
                'required' => $this->required,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return true;
    }
}
