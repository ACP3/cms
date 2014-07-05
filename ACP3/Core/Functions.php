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
     * @var \Doctrine\DBAL\Connection
     */
    protected static $db;
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
        if (!static::$db) {
            static::$db = Registry::get('Db');
            static::$view = Registry::get('View');
        }
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
            ->setRecipients($to)
            ->send();
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
            } catch (\Exception $e) {
                static::$db->rollback();
            }
        }
        return false;
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