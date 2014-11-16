<?php
namespace ACP3\Core\Helpers;

use ACP3\Core;

/**
 * Class StringFormatter
 * @package ACP3\Core\Helpers
 */
class StringFormatter
{

    /**
     * Macht einen String URL sicher
     *
     * @param string $var Die unzuwandelnde Variable
     *
     * @return string
     */
    public static function makeStringUrlSafe($var)
    {
        $var = strip_tags($var);
        if (!preg_match('/&([a-z]+);/', $var)) {
            $var = htmlentities($var, ENT_QUOTES, 'UTF-8');
        }
        $search = array(
            '/&([a-z]{1})uml;/',
            '/&szlig;/',
            '/&([a-z0-9]+);/',
            '/(\s+)/',
            '/-{2,}/',
            '/[^a-z0-9-]/',
        );
        $replace = array(
            '${1}e',
            'ss',
            '',
            '-',
            '-',
            '',
        );
        return preg_replace($search, $replace, strtolower($var));
    }

    /**
     * Konvertiert Zeilenumbrüche zu neuen Absätzen
     *
     * @param string  $data
     * @param boolean $isXhtml
     * @param boolean $lineBreaks
     *
     * @return string
     */
    public function nl2p($data, $isXhtml = true, $lineBreaks = false)
    {
        $data = trim($data);
        if ($lineBreaks === true) {
            return '<p>' . preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '<br' . ($isXhtml == true ? ' /' : '') . '>'), $data) . '</p>';
        } else {
            return '<p>' . preg_replace("/([\n]{1,})/i", "</p>\n<p>", $data) . '</p>';
        }
    }

    /**
     * Kürzt einen String, welcher im UTF-8-Charset vorliegt
     * auf eine bestimmte Länge
     *
     * @param string  $data
     *    Der zu kürzende String
     * @param integer $chars
     *    Die anzuzeigenden Zeichen
     * @param integer $diff
     *    Anzahl der Zeichen, welche nach strlen($data) - $chars noch kommen müssen
     * @param string  $append
     *    Kann bspw. dazu genutzt werden, um an den gekürzten Text noch einen Weiterlesen-Link anzuhängen
     *
     * @return string
     */
    public function shortenEntry($data, $chars = 300, $diff = 50, $append = '')
    {
        if ($chars <= $diff) {
            $diff = 0;
        }

        $shortened = utf8_decode(html_entity_decode(strip_tags($data), ENT_QUOTES, 'UTF-8'));
        if (strlen($shortened) > $chars && strlen($shortened) - $chars >= $diff) {
            return utf8_encode(substr($shortened, 0, $chars - $diff)) . $append;
        }
        return $data;
    }
}