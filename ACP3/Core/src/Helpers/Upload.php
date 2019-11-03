<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;

class Upload
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var string
     */
    private $directory = '';

    /**
     * Upload constructor.
     */
    public function __construct(ApplicationPath $appPath, string $directory)
    {
        $this->appPath = $appPath;
        $this->directory = $directory;
    }

    /**
     * Hochgeladene Dateien verschieben und umbenennen.
     *
     * @param string $tmpFilename Temporäre Datei
     * @param string $filename    Dateiname
     *
     * @return array
     *
     * @throws ValidationFailedException
     */
    public function moveFile(string $tmpFilename, string $filename, bool $retainFilename = false)
    {
        $path = $this->appPath->getUploadsDir() . $this->directory . '/';

        if (!\is_dir($path)) {
            $result = @\mkdir($path);

            if (!$result) {
                throw new ValidationFailedException([\sprintf('Could not create folder "%s"', $this->directory)]);
            }
        }

        if ($retainFilename === true) {
            $newFilename = $filename;
        } else {
            $newFilename = 1;
            $ext = \strtolower(\strrchr($filename, '.'));

            // Dateiname solange ändern, wie eine Datei mit dem selben Dateinamen im aktuellen Ordner existiert
            while (\is_file($path . $newFilename . $ext) === true) {
                ++$newFilename;
            }

            $newFilename .= $ext;
        }

        if (\is_writable($path) === true) {
            if (!@\move_uploaded_file($tmpFilename, $path . $newFilename)) {
                return [];
            }
            $return = [];
            $return['name'] = $newFilename;
            $return['size'] = $this->calcFilesize(\filesize($path . $return['name']));

            return $return;
        }

        return [];
    }

    /**
     * Ermittelt die Dateigröße gemäß IEC 60027-2.
     *
     * @param int $value Die Dateigröße in Byte
     *
     * @return string Die Dateigröße als Fließkommazahl mit der dazugehörigen Einheit
     */
    public function calcFilesize(int $value)
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

        return \round($value, 2) . ' ' . $units[$i];
    }

    /**
     * Löscht eine Datei im uploads Ordner.
     *
     * @return bool
     */
    public function removeUploadedFile(string $file)
    {
        $path = $this->appPath->getUploadsDir() . $this->directory . '/' . $file;
        if (!empty($file) && !\preg_match('=/=', $file) && \is_file($path) === true) {
            return \unlink($path);
        }

        return false;
    }
}
