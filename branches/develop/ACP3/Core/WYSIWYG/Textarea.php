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
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    private $emoticonsHelpers;

    /**
     * @param \ACP3\Core\Modules $modules
     */
    public function __construct(Core\Modules $modules)
    {
        $this->modules = $modules;
    }

    /**
     * @param $emoticonsHelpers
     *
     * @return $this
     */
    public function setEmoticonsHelpers(\ACP3\Modules\ACP3\Emoticons\Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;

        return $this;
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
    public function display()
    {
        $out = '';
        if ($this->modules->isActive('emoticons') === true) {
            $out .= $this->emoticonsHelpers->emoticonsList($this->id);
        }
        $out .= '<textarea name="' . $this->name . '" id="' . $this->id . '" cols="60" rows="6" class="form-control">' . $this->value . '</textarea>';
        return $out;
    }
}
