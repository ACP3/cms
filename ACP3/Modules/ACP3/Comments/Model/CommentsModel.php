<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Model;


use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Comments\Installer\Schema;

class CommentsModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;
}
