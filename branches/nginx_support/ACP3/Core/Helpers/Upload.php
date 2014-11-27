<?php
namespace ACP3\Core\Helpers;

use ACP3\Core\Exceptions;

/**
 * Class Upload
 * @package ACP3\Core\Helpers
 */
class Upload
{
    /**
     * @var string
     */
    private $directory = '';

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * Hochgeladene Dateien verschieben und umbenennen
     *
     * @param string $tmpFilename
     *  Temporäre Datei
     * @param string $filename
     *  Dateiname
     * @param bool   $retainFilename
     *
     * @return array
     */
    public function moveFile($tmpFilename, $filename, $retainFilename = false)
    {
        $path = UPLOADS_DIR . $this->directory . '/';

        if ($retainFilename === true) {
            $newFilename = $filename;
        } else {
            $newFilename = 1;
            $ext = strrchr($filename, '.');

            // Dateiname solange ändern, wie eine Datei mit dem selben Dateinamen im aktuellen Ordner existiert
            while (is_file($path . $newFilename . $ext) === true) {
                ++$newFilename;
            }

            $newFilename .= $ext;
        }

        if (is_writable($path) === true) {
            if (!@move_uploaded_file($tmpFilename, $path . $newFilename)) {
                return [];
            } else {
                $return = [];
                $return['name'] = $newFilename;
                $return['size'] = $this->calcFilesize(filesize($path . $return['name']));

                return $return;
            }
        }
        return [];
    }

    /**
     * Ermittelt die Dateigröße gemäß IEC 60027-2
     *
     * @param integer $value
     *    Die Dateigröße in Byte
     *
     * @return string
     *    Die Dateigröße als Fließkommazahl mit der dazugehörigen Einheit
     */
    public function calcFilesize($value)
    {
        $units = [
            0 => 'Byte',
            1 => 'KiB',
            2 => 'MiB',
            3 => 'GiB',
            4 => 'TiB',
            5 => 'PiB',
            6 => 'EiB',
            7 => 'ZiB',
            8 => 'YiB',
        ];

        for ($i = 0; $value >= 1024; ++$i) {
            $value = $value / 1024;
        }

        return round($value, 2) . ' ' . $units[$i];
    }

    /**
     * Löscht eine Datei im uploads Ordner
     *
     * @param string $file
     *    Der Name der Datei
     *
     * @return boolean
     */
    public function removeUploadedFile($file)
    {
        $path = UPLOADS_DIR . $this->directory . '/' . $file;
        if (!empty($dir) && !empty($file) && !preg_match('=/=', $file) && is_file($path) === true) {
            return @unlink($path);
        }
        return false;
    }
}
