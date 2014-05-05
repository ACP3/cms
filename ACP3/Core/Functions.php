<?php

namespace ACP3\Core;

/**
 * Manages the most used functions in the ACP3
 *
 * @author Tino Goratsch
 */
class Functions
{
    /**
     * @var Auth
     */
    protected static $auth;
    /**
     * @var Breadcrumb
     */
    protected static $breadcrumb;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected static $db;
    /**
     * @var Lang
     */
    protected static $lang;
    /**
     * @var SEO
     */
    protected static $seo;
    /**
     * @var URI
     */
    protected static $uri;
    /**
     * @var View
     */
    protected static $view;

    private static $dataTableInitialized = false;

    /**
     * Set the class attributes
     */
    protected static function _init()
    {
        if (!static::$auth) {
            static::$auth = Registry::get('Auth');
            static::$breadcrumb = Registry::get('Breadcrumb');
            static::$db = Registry::get('Db');
            static::$lang = Registry::get('Lang');
            static::$seo = Registry::get('SEO');
            static::$uri = Registry::get('URI');
            static::$view = Registry::get('View');
        }
    }

    /**
     * Ermittelt die Dateigröße gemäß IEC 60027-2
     *
     * @param integer $value
     *    Die Dateigröße in Byte
     * @return string
     *    Die Dateigröße als Fließkommazahl mit der dazugehörigen Einheit
     */
    public static function calcFilesize($value)
    {
        $units = array(
            0 => 'Byte',
            1 => 'KiB',
            2 => 'MiB',
            3 => 'GiB',
            4 => 'TiB',
            5 => 'PiB',
            6 => 'EiB',
            7 => 'ZiB',
            8 => 'YiB',
        );

        for ($i = 0; $value >= 1024; ++$i) {
            $value = $value / 1024;
        }

        return round($value, 2) . ' ' . $units[$i];
    }

    /**
     * Displays a confirm box
     *
     * @param string $text
     * @param int|string|array $forward
     * @param int|string $backward
     * @param integer $overlay
     * @return string
     */
    public static function confirmBox($text, $forward = 0, $backward = 0, $overlay = 0)
    {
        if (!empty($text)) {
            static::_init();

            $confirm = array(
                'text' => $text,
                'forward' => $forward,
                'overlay' => $overlay,
            );
            if (!empty($backward)) {
                $confirm['backward'] = $backward;
            }

            static::$view->assign('confirm', $confirm);

            return static::$view->fetchTemplate('system/confirm_box.tpl');
        }
        return '';
    }

    /**
     * Displays a confirm box, where the forward button triggers a form submit using POST
     *
     * @param $text
     * @param array $data
     * @param $forward
     * @param int $backward
     * @return string
     */
    public static function confirmBoxPost($text, array $data, $forward, $backward = 0)
    {
        if (!empty($text) && !empty($data)) {
            static::_init();

            $confirm = array(
                'text' => $text,
                'data' => $data,
                'forward' => $forward,
            );
            if (!empty($backward)) {
                $confirm['backward'] = $backward;
            }

            static::$view->assign('confirm', $confirm);

            return static::$view->fetchTemplate('system/confirm_box_post.tpl');
        }
        return '';
    }

    /**
     *
     * @param array $config
     * @return string
     */
    public static function dataTable(array $config)
    {
        static::_init();

        static::$view->enableJsLibraries(array('datatables'));

        if (isset($config['records_per_page']) === false) {
            $config['records_per_page'] = Registry::get('Auth')->entries;
        }

        $config['initialized'] = self::$dataTableInitialized;

        static::$view->assign('dt', $config);
        self::$dataTableInitialized = true;

        return static::$view->fetchTemplate('system/data_table.tpl');
    }

    /**
     * Gibt eine Box mit den aufgetretenen Fehlern aus
     *
     * @param string|array $errors
     * @return string
     */
    public static function errorBox($errors)
    {
        static::_init();

        $hasNonIntegerKeys = false;
        if (is_array($errors) === true) {
            foreach (array_keys($errors) as $key) {
                if (Validate::isNumber($key) === false) {
                    $hasNonIntegerKeys = true;
                    break;
                }
            }
        } else {
            $errors = (array)$errors;
        }
        static::$view->assign('error_box', array('non_integer_keys' => $hasNonIntegerKeys, 'errors' => $errors));
        $content = static::$view->fetchTemplate('system/error_box.tpl');

        if (static::$uri->getIsAjax() === true) {
            $return = array(
                'success' => false,
                'content' => $content,
            );

            self::outputJson($return);
        }
        return $content;
    }

    public static function outputJson(array $data)
    {
        header('Content-type: application/json; charset="UTF-8"');

        echo json_encode($data);
        exit;
    }

    /**
     * Generiert eine E-Mail und versendet diese
     *
     * @param string $recipientName
     *    Name des Empfängers
     * @param string $recipientEmail
     *    E-Mail-Adresse des Empfängers
     * @param string $from
     *    E-mail-Adresse des Versenders
     * @param string $subject
     *    Betreff der E-Mail
     * @param string $body
     *    E-Mail-Body
     * @param string $mailSignature
     * @return boolean|string
     */
    public static function generateEmail($recipientName, $recipientEmail, $from, $subject, $body, $mailSignature = '')
    {
        if (!empty($recipientName)) {
            $to = array(
                'name' => $recipientName,
                'email' => $recipientEmail
            );
        } else {
            $to = $recipientEmail;
        }

        $mailer = new Mailer(self::$view);
        return $mailer
            ->setSubject($subject)
            ->setBody($body)
            ->setMailSignature($mailSignature)
            ->setFrom($from)
            ->setTo($to)
            ->send();
    }

    /**
     * Generiert ein gesalzenes Passwort
     *
     * @param string $salt
     *    Das zu verwendende Salz
     * @param string $plaintext
     *    Das Passwort in Klartextform, welches verschlüsselt werden soll
     * @param string $algorithm
     *    Der zu verwendende Hash-Algorithmus
     * @return string
     */
    public static function generateSaltedPassword($salt, $plaintext, $algorithm = 'sha1')
    {
        return hash($algorithm, $salt . hash($algorithm, $plaintext));
    }

    /**
     * Generiert ein Inhaltsverzeichnis
     *
     * @param array $pages
     * @param string $path
     * @param boolean $titlesFromDb
     * @param boolean $customUris
     * @return string
     */
    protected static function _generateTOC(array $pages, $path = '', $titlesFromDb = false, $customUris = false)
    {
        if (!empty($pages)) {
            static::_init();

            $uri = static::$uri;
            $path = empty($path) ? $uri->getUriWithoutPages() : $path;
            $toc = array();
            $i = 0;
            foreach ($pages as $page) {
                $page_num = $i + 1;
                if ($titlesFromDb === false) {
                    $attributes = self::_getHtmlAttributes($page);
                    $toc[$i]['title'] = !empty($attributes['title']) ? $attributes['title'] : sprintf(static::$lang->t('system', 'toc_page'), $page_num);
                } else {
                    $toc[$i]['title'] = !empty($page['title']) ? $page['title'] : sprintf(static::$lang->t('system', 'toc_page'), $page_num);
                }

                $toc[$i]['uri'] = $customUris === true ? $page['uri'] : $uri->route($path) . ($page_num > 1 ? 'page_' . $page_num . '/' : '');

                $toc[$i]['selected'] = false;
                if ($customUris === true) {
                    if ($page['uri'] === $uri->route($uri->query) ||
                        $uri->route($uri->query) === $uri->route($uri->mod . '/' . $uri->file) && $i == 0
                    ) {
                        $toc[$i]['selected'] = true;
                        static::$breadcrumb->setTitlePostfix($toc[$i]['title']);
                    }
                } else {
                    if ((Validate::isNumber($uri->page) === false && $i === 0) || $uri->page === $page_num) {
                        $toc[$i]['selected'] = true;
                        static::$breadcrumb->setTitlePostfix($toc[$i]['title']);
                    }
                }
                ++$i;
            }
            static::$view->assign('toc', $toc);
            return static::$view->fetchTemplate('system/toc.tpl');
        }
        return '';
    }

    /**
     * Liest aus einem String alle vorhandenen HTML-Attribute ein und
     * liefert diese als assoziatives Array zurück
     *
     * @param string $string
     * @return array
     */
    protected static function _getHtmlAttributes($string)
    {
        $matches = array();
        preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $matches);

        $return = array();
        if (!empty($matches)) {
            $c_matches = count($matches[1]);
            for ($i = 0; $i < $c_matches; ++$i)
                $return[$matches[1][$i]] = $matches[2][$i];
        }

        return $return;
    }

    /**
     * Parst einen Text und zerlegt diesen bei Bedarf mehrere Seiten
     *
     * @param string $text
     *    Der zu parsende Text
     * @param string $path
     *    Der ACP3-interne URI-Pfad, um die Links zu generieren
     * @return string|array
     */
    public static function splitTextIntoPages($text, $path)
    {
        // Falls keine Seitenumbrüche vorhanden sein sollten, Text nicht unnötig bearbeiten
        if (strpos($text, 'class="page-break"') === false) {
            return $text;
        } else {
            static::_init();

            $regex = '/<hr(.+)class="page-break"(.*)(\/>|>)/iU';

            $pages = preg_split($regex, $text, -1, PREG_SPLIT_NO_EMPTY);
            $c_pages = count($pages);

            // Falls zwar Seitenumbruch gesetzt ist, aber danach
            // kein weiterer Text kommt, den unbearbeiteten Text ausgeben
            if ($c_pages == 1) {
                return $text;
            } else {
                $matches = array();
                preg_match_all($regex, $text, $matches);

                $currentPage = Validate::isNumber(static::$uri->page) === true && static::$uri->page <= $c_pages ? static::$uri->page : 1;
                $nextPage = !empty($pages[$currentPage]) ? static::$uri->route($path) . 'page_' . ($currentPage + 1) . '/' : '';
                $previousPage = $currentPage > 1 ? static::$uri->route($path) . ($currentPage - 1 > 1 ? 'page_' . ($currentPage - 1) . '/' : '') : '';

                if (!empty($nextPage)) {
                    static::$seo->setNextPage($nextPage);
                }
                if (!empty($previousPage)) {
                    static::$seo->setPreviousPage($previousPage);
                }

                $page = array(
                    'toc' => self::_generateTOC($matches[0], $path),
                    'text' => $pages[$currentPage - 1],
                    'next' => $nextPage,
                    'previous' => $previousPage,
                );

                return $page;
            }
        }
    }

    /**
     * Holt sich die von setRedirectMessage() erzeugte Redirect Nachricht
     */
    public static function getRedirectMessage()
    {
        if (isset($_SESSION['redirect_message']) && is_array($_SESSION['redirect_message'])) {
            static::_init();

            static::$view->assign('redirect', $_SESSION['redirect_message']);
            static::$view->assign('redirect_message', static::$view->fetchTemplate('system/redirect_message.tpl'));
            unset($_SESSION['redirect_message']);
        }
    }

    /**
     * Setzt eine Redirect Nachricht
     *
     * @param $success
     * @param $text
     * @param $path
     * @param bool $overlay
     */
    public static function setRedirectMessage($success, $text, $path, $overlay = false)
    {
        if (empty($text) === false && empty($path) === false) {
            static::_init();

            $_SESSION['redirect_message'] = array(
                'success' => is_int($success) ? true : (bool)$success,
                'text' => $text
            );
            if ($overlay === true) {
                static::$view->setContentTemplate('system/close_overlay.tpl');
                return;
            }

            static::$uri->redirect($path);
        }
    }

    /**
     * Gibt zurück, ob der aktuelle User Agent ein mobiler Browser ist, oder nicht.
     *
     * @return boolean
     * @see http://detectmobilebrowsers.com/download/php
     */
    public static function isMobileBrowser()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $userAgent) ||
            preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Macht einen String URL sicher
     *
     * @param string $var
     *    Die unzuwandelnde Variable
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
     * Hochgeladene Dateien verschieben und umbenennen
     *
     * @param string $tmpFilename
     *  Temporäre Datei
     * @param string $filename
     *  Dateiname
     * @param string $dir
     *  Ordner, in den die Datei verschoben werden soll
     * @throws Exceptions\ValidationFailed
     * @return array
     *  Gibt ein Array mit dem Namen und der Größe der neuen Datei zurück
     */
    public static function moveFile($tmpFilename, $filename, $dir)
    {
        $path = UPLOADS_DIR . $dir . '/';
        $ext = strrchr($filename, '.');
        $newName = 1;

        // Dateiname solange ändern, wie eine Datei mit dem selben Dateinamen im aktuellen Ordner existiert
        while (is_file($path . $newName . $ext) === true) {
            ++$newName;
        }

        if (is_writable($path) === true) {
            if (!@move_uploaded_file($tmpFilename, $path . $newName . $ext)) {
                static::_init();

                $error = array(
                    sprintf(static::$lang->t('system', 'upload_error'), $filename)
                );
                throw new Exceptions\ValidationFailed(self::errorBox($error));
            } else {
                $newFile = array();
                $newFile['name'] = $newName . $ext;
                $newFile['size'] = self::calcFilesize(filesize($path . $newFile['name']));

                return $newFile;
            }
        }
        return array();
    }

    /**
     * Verschiebt einen DB-Eintrag um einen Schritt nach oben bzw. unten
     *
     * @param string $action
     *    up = einen Schritt nach oben verschieben
     *    down = einen Schritt nach unten verschieben
     * @param string $table
     *    Die betroffene Tabelle
     * @param string $idField
     *    Name des ID-Feldes
     * @param string $sortField
     *    Name des Sortier-Feldes. damit die Sortierung geändert werden kann
     * @param string $id
     *    Die ID des Datensatzes, welcher umsortiert werden soll
     * @param string $where
     *    Optionales Vergleichsfeld, um den richtigen Vorgänger/Nachfolger bestimmen zu können
     * @return boolean
     */
    public static function moveOneStep($action, $table, $idField, $sortField, $id, $where = '')
    {
        if ($action === 'up' || $action === 'down') {
            static::_init();

            static::$db->beginTransaction();
            try {
                $id = (int)$id;
                $table = DB_PRE . $table;

                // Zusätzliche WHERE-Bedingung
                $where = !empty($where) ? 'a.' . $where . ' = b.' . $where . ' AND ' : '';

                // Ein Schritt nach oben
                if ($action === 'up') {
                    // Aktuelles Element und das vorherige Element selektieren
                    $query = static::$db->fetchAssoc('SELECT a.' . $idField . ' AS other_id, a.' . $sortField . ' AS other_sort, b.' . $sortField . ' AS elem_sort FROM ' . $table . ' AS a, ' . $table . ' AS b WHERE ' . $where . 'b.' . $idField . ' = ' . $id . ' AND a.' . $sortField . ' < b.' . $sortField . ' ORDER BY a.' . $sortField . ' DESC LIMIT 1');
                    // Ein Schritt nach unten
                } else {
                    // Aktuelles Element und das nachfolgende Element selektieren
                    $query = static::$db->fetchAssoc('SELECT a.' . $idField . ' AS other_id, a.' . $sortField . ' AS other_sort, b.' . $sortField . ' AS elem_sort FROM ' . $table . ' AS a, ' . $table . ' AS b WHERE ' . $where . 'b.' . $idField . ' = ' . $id . ' AND a.' . $sortField . ' > b.' . $sortField . ' ORDER BY a.' . $sortField . ' ASC LIMIT 1');
                }

                if (!empty($query)) {
                    // Sortierreihenfolge des aktuellen Elementes zunächst auf 0 setzen
                    // um Probleme mit möglichen Duplicate-Keys zu umgehen
                    static::$db->update($table, array($sortField => 0), array($idField => $id));
                    static::$db->update($table, array($sortField => $query['elem_sort']), array($idField => $query['other_id']));
                    // Element nun den richtigen Wert zuweisen
                    static::$db->update($table, array($sortField => $query['other_sort']), array($idField => $id));

                    static::$db->commit();
                    return true;
                }
            } catch (Exception $e) {
                static::$db->rollback();
            }
        }
        return false;
    }

    /**
     * Konvertiert Zeilenumbrüche zu neuen Absätzen
     *
     * @param string $data
     * @param boolean $isXhtml
     * @param boolean $lineBreaks
     * @return string
     */
    public static function nl2p($data, $isXhtml = true, $lineBreaks = false)
    {
        $data = trim($data);
        if ($lineBreaks === true) {
            return '<p>' . preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '<br' . ($isXhtml == true ? ' /' : '') . '>'), $data) . '</p>';
        } else {
            return '<p>' . preg_replace("/([\n]{1,})/i", "</p>\n<p>", $data) . '</p>';
        }
    }

    /**
     * Liefert ein Array zur Ausgabe als Dropdown-Menü
     * für die Anzahl der anzuzeigenden Datensätze je Seite
     *
     * @param integer $currentValue
     * @param integer $steps
     * @param integer $maxValue
     * @return array
     */
    public static function recordsPerPage($currentValue, $steps = 5, $maxValue = 50)
    {
        $records = array();
        for ($i = 0, $j = $steps; $j <= $maxValue; $i++, $j += $steps) {
            $records[$i]['value'] = $j;
            $records[$i]['selected'] = self::selectEntry('entries', $j, $currentValue);
        }
        return $records;
    }

    /**
     * Löscht eine Datei im uploads Ordner
     *
     * @param string $dir
     *    Der Ordner, in welchem die Datei liegt
     * @param string $file
     *    Der Name der Datei
     * @return boolean
     */
    public static function removeUploadedFile($dir, $file)
    {
        $path = UPLOADS_DIR . $dir . '/' . $file;
        if (!empty($dir) && !empty($file) && !preg_match('=/=', $file) && is_file($path) === true) {
            return @unlink($path);
        }
        return false;
    }

    /**
     * Ersetzt interne ACP3 interne URIs in Texten mit ihren jeweiligen Aliasen
     *
     * @param string $text
     * @return string
     */
    public static function rewriteInternalUri($text)
    {
        $rootDir = str_replace('/', '\/', ROOT_DIR);
        $host = $_SERVER['HTTP_HOST'];
        return preg_replace_callback('/<a href="(http(s?):\/\/' . $host . ')?(' . $rootDir . ')?(index\.php)?(\/?)((?i:[a-z\d_\-]+\/){2,})"/', "\\ACP3\\Core\\Functions::rewriteInternalUriCallback", $text);
    }

    /**
     * Callback-Funktion zum Ersetzen der ACP3 internen URIs gegen ihre Aliase
     *
     * @param string $matches
     * @return string
     */
    public static function rewriteInternalUriCallback($matches)
    {
        static::_init();

        return '<a href="' . static::$uri->route($matches[6], 1) . '"';
    }

    /**
     * Generiert einen Zufallsstring beliebiger Länge
     *
     * @param integer $strLength
     *  Länge des zufälligen Strings
     * @return string
     */
    public static function salt($strLength)
    {
        $salt = '';
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $c_chars = strlen($chars) - 1;
        while (strlen($salt) < $strLength) {
            $char = $chars[mt_rand(0, $c_chars)];
            // Zeichen nur hinzufügen, wenn sich dieses nicht bereits im Salz befindet
            if (strpos($salt, $char) === false) {
                $salt .= $char;
            }
        }
        return $salt;
    }

    /**
     * Selektion eines Eintrages in einem Dropdown-Menü
     *
     * @param string $name
     *  Name des Feldes im Formular
     * @param mixed $defValue
     *  Abzugleichender Parameter mit $currentValue
     * @param mixed $currentValue
     *  Wert aus der SQL Tabelle
     * @param string $attr
     *  HTML-Attribut, um Eintrag zu selektieren
     * @return string
     */
    public static function selectEntry($name, $defValue, $currentValue = '', $attr = 'selected')
    {
        $attr = ' ' . $attr . '="' . $attr . '"';

        if (isset($_POST[$name]) === true) {
            $currentValue = $_POST[$name];
        }

        if (is_array($currentValue) === false && $currentValue == $defValue) {
            return $attr;
        } elseif (is_array($currentValue) === true) {
            foreach ($currentValue as $row) {
                if ($row == $defValue) {
                    return $attr;
                }
            }
        }

        return '';
    }

    /**
     *
     * @param string $name
     * @param array $values
     * @param array $lang
     * @param string|integer $currentValue
     * @param string $selected
     * @return array
     */
    public static function selectGenerator($name, array $values, array $lang, $currentValue = '', $selected = 'selected')
    {
        $array = array();
        if (count($values) == count($lang)) {
            $c_array = count($values);
            $id = str_replace('_', '-', $name);
            for ($i = 0; $i < $c_array; ++$i) {
                $array[] = array(
                    'value' => $values[$i],
                    'id' => ($selected == 'checked' ? $id . '-' . $values[$i] : ''),
                    $selected => self::selectEntry($name, $values[$i], $currentValue, $selected),
                    'lang' => $lang[$i]
                );
            }
        }
        return $array;
    }

    /**
     * Kürzt einen String, welcher im UTF-8-Charset vorliegt
     * auf eine bestimmte Länge
     *
     * @param string $data
     *    Der zu kürzende String
     * @param integer $chars
     *    Die anzuzeigenden Zeichen
     * @param integer $diff
     *    Anzahl der Zeichen, welche nach strlen($data) - $chars noch kommen müssen
     * @param string $append
     *    Kann bspw. dazu genutzt werden, um an den gekürzten Text noch einen Weiterlesen-Link anzuhängen
     * @return string
     */
    public static function shortenEntry($data, $chars = 300, $diff = 50, $append = '')
    {
        if ($chars <= $diff)
            $diff = 0;

        $shortened = utf8_decode(html_entity_decode(strip_tags($data), ENT_QUOTES, 'UTF-8'));
        if (strlen($shortened) > $chars && strlen($shortened) - $chars >= $diff) {
            return utf8_encode(substr($shortened, 0, $chars - $diff)) . $append;
        }
        return $data;
    }

    /**
     * Enkodiert alle HTML-Entitäten eines Strings
     * zur Vermeidung von XSS
     *
     * @param string $var
     * @param boolean $scriptTagOnly
     * @return string
     */
    public static function strEncode($var, $scriptTagOnly = false)
    {
        $var = preg_replace('=<script[^>]*>.*</script>=isU', '', $var);
        return $scriptTagOnly === true ? $var : htmlentities($var, ENT_QUOTES, 'UTF-8');
    }

}