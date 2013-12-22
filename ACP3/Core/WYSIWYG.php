<?php

namespace ACP3\Core;

/**
 * Wrapper class for the WYSIWYG editors
 *
 * @author Tino Goratsch
 */
class WYSIWYG
{

    /**
     *
     * @var \ACP3\Core\WYSIWYG\AbstractWYSIWYG
     */
    protected static $editor = null;

    public static function factory($editor, array $params = array())
    {
        $path = CLASSES_DIR . 'WYSIWYG/' . $editor . '.php';
        if (is_file($path) === true) {
            if (isset($params['toolbar']) === false)
                $params['toolbar'] = '';
            if (isset($params['advanced']) === false)
                $params['advanced'] = false;
            if (isset($params['height']) === false)
                $params['height'] = '';

            $className = "\\ACP3\\Core\\WYSIWYG\\$editor";
            self::$editor = new $className($params['id'], $params['name'], $params['value'], $params['toolbar'], (bool)$params['advanced'], (int)$params['height']);
        } else {
            throw new \Exception('File ' . $path . ' not found!');
        }
    }

    public function display()
    {
        return self::$editor->display();
    }
}