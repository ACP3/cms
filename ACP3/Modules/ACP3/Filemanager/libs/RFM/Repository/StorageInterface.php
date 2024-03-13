<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Repository;

interface StorageInterface
{
    /**
     * Return storage name string.
     */
    public function getName(): string;

    /**
     * Set configuration options for storage.
     * Merge config file options array with custom options array.
     */
    public function setConfig(array $options): void;

    /**
     * Get configuration options specific for storage.
     */
    public function config(array|string|null $key = null, mixed $default = null): mixed;

    /**
     * Set user storage folder.
     */
    public function setRoot(string $path, bool $makeDir, bool $relativeToDocumentRoot = false): void;

    /**
     * Get user storage folder.
     */
    public function getRoot(): string;

    /**
     * Get user storage folder without document root.
     */
    public function getDynamicRoot(): string;

    /**
     * Return path without storage root path.
     */
    public function getRelativePath(string $path): string;

    /**
     * Create new folder.
     *
     * @param $options array
     */
    public function createFolder(ItemModelInterface $target, ?ItemModelInterface $prototype, array $options): bool;

    /**
     * Retrieve mime type of file.
     *
     * @param string $path - absolute or relative path
     */
    public function getMimeType(string $path): string;

    /**
     * Defines size of file.
     */
    public function getFileSize(string $path): string|int;

    /**
     * Return summary info for specified folder.
     */
    public function getDirSummary(string $dir, array &$result): array;
}
