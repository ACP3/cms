<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Repository;

interface ItemModelInterface
{
    /**
     * Get storage instance associated with model item.
     */
    public function getStorage(): StorageInterface;

    /**
     * Associate storage with model item.
     */
    public function setStorage(string $storageName): void;

    /**
     * Return relative path to item.
     */
    public function getRelativePath(): string;

    /**
     * Return absolute path to item.
     */
    public function getAbsolutePath(): string;

    /**
     * Return path without storage root path, prepended with dynamic folder.
     * Based on relative item path.
     */
    public function getDynamicPath();

    /**
     * Return thumbnail relative path from given path.
     * Work for both files and dirs paths.
     */
    public function getThumbnailPath(): string;

    /**
     * Return original relative path for thumbnail model.
     * Work for both files and dirs paths.
     */
    public function getOriginalPath(): string;

    /**
     * Validate whether item is file or folder.
     */
    public function isDirectory(): bool;

    /**
     * Validate whether file or folder exists.
     */
    public function isExists(): bool;

    /**
     * Check whether the item is root folder.
     */
    public function isRoot(): bool;

    /**
     * Check whether file is image, based on its mime type.
     */
    public function isImageFile(): string;

    /**
     * Check whether item path is valid by comparing paths.
     */
    public function isValidPath(): bool;

    /**
     * Check the patterns blacklist for path.
     */
    public function isAllowedPattern(): bool;

    /**
     * Check the global blacklists for this file path.
     *
     * @return bool
     */
    public function isUnrestricted();

    /**
     * Verify if item has read permission.
     */
    public function hasReadPermission(): bool;

    /**
     * Verify if item has write permission.
     */
    public function hasWritePermission(): bool;

    /**
     * Check that item exists and path is valid.
     */
    public function checkPath(): void;

    /**
     * Check that item has read permission.
     */
    public function checkReadPermission(): void;

    /**
     * Check that item can be written to.
     */
    public function checkWritePermission(): void;

    /**
     * Build and return item data class instance.
     */
    public function getData(): ItemData;

    /**
     * Return model for parent folder on the current item.
     * Create and cache if not existing yet.
     */
    public function closest(): ?self;

    /**
     * Return model for thumbnail of the current item.
     * Create and cache if not existing yet.
     */
    public function thumbnail(): ?self;

    /**
     * Create thumbnail from the original image.
     */
    public function createThumbnail(): void;

    /**
     * Remove current file or folder.
     */
    public function remove(): bool;
}
