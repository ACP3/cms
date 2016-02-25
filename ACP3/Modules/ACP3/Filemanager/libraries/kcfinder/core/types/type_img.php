<?php

/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace kcfinder;

class type_img
{

    public function checkFile($file, array $config)
    {
        $driver = isset($config['imageDriversPriority'])
            ? image::getDriver(explode(" ", $config['imageDriversPriority'])) : "gd";

        $img = image::factory($driver, $file);

        if ($img->initError) {
            return "Unknown image format/encoding.";
        }

        return true;
    }
}
