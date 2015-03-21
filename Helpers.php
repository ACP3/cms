<?php
namespace ACP3\Modules\ACP3\Emoticons;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Emoticons
 */
class Helpers
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Cache
     */
    protected $emoticonsCache;
    /**
     * @var Core\View
     */
    protected $view;

    /**
     * @param Core\View $view
     * @param Cache     $emoticonsCache
     */
    public function __construct(Core\View $view, Cache $emoticonsCache)
    {
        $this->view = $view;
        $this->emoticonsCache = $emoticonsCache;
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
        $this->view->assign('emoticons', $this->emoticonsCache->getCache());
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
        return strtr($string, $this->emoticonsCache->getCache());
    }
}