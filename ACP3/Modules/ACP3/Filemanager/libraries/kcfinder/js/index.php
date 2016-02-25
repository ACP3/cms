<?php

/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace kcfinder;

chdir("..");
require "core/autoload.php";
$min = new minifier("js");
$min->minify("cache/base.js");
