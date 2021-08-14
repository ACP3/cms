<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

interface MigrationInterface
{
    public function getSchemaVersion(): int;

    public function up(): void;

    public function down(): void;
}
