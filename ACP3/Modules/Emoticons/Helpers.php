<?php
/**
 * Emoticons
 *
 * @author     Tino Goratsch
 * @package    ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Emoticons;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\Emoticons
 */
class Helpers
{
    /**
     * @var array
     */
    protected $emoticons = array();

    /**
     * @var Core\View
     */
    protected $view;

    public function __construct(Core\View $view, Cache $emoticonsCache)
    {
        $this->view = $view;

        // Initialize emoticons
        $this->emoticons = $emoticonsCache->getCache();
    }

    /**
     * Erzeugt eine Auflistung der Emoticons
     *
     * @param string $formFieldId
     *    Die ID des Eingabefeldes, in welches die Emoticons eingefügt werden sollen
     *
     * @return string
     */
    public function emoticonsList($formFieldId = '')
    {
        $this->view->assign('emoticons_field_id', empty($formFieldId) ? 'message' : $formFieldId);
        $this->view->assign('emoticons', $this->emoticons);
        return $this->view->fetchTemplate('emoticons/emoticons.tpl');
    }

    /**
     * Ersetzt bestimmte Zeichen durch Emoticons
     *
     * @param string $string
     *  Zu durchsuchender Text nach Zeichen
     *
     * @return string
     */
    public function emoticonsReplace($string)
    {
        return strtr($string, $this->emoticons);
    }

}
