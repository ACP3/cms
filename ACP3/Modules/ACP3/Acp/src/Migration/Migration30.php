<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Acp\Migration;

use ACP3\Core\Migration\MigrationInterface;

class Migration30 implements MigrationInterface
{
    public function getSchemaVersion(): int
    {
        return 30;
    }

    public function up(): void
    {
    }

    public function down(): void
    {
    }
}
