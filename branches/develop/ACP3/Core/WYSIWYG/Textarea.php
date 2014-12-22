<?php

namespace ACP3\Core\WYSIWYG;

/**
 * Implementation of the AbstractWYSIWYG class for a simple textarea
 * @package ACP3\Core\WYSIWYG
 */
class Textarea extends AbstractWYSIWYG
{
    /**
     * @param array $params
     */
    public function setParameters(array $params = [])
    {
        $this->id = $params['id'];
        $this->name = $params['name'];
        $this->value = $params['value'];
        $this->advanced = isset($params['advanced']) ? (bool)$params['advanced'] : false;
    }

    /**
     * @return string
     */
    public function display()
    {
        $out = '';
        if ($this->container->get('core.modules')->isActive('emoticons') === true) {
            $out .= $this->container->get('emoticons.helpers')->emoticonsList($this->id);
        }
        $out .= '<textarea name="' . $this->name . '" id="' . $this->id . '" cols="50" rows="6" class="form-control">' . $this->value . '</textarea>';
        return $out;
    }
}
