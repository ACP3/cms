<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Api;

interface ApiInterface
{
    /**
     * Return server-side data to override on the client-side.
     */
    public function actionInitiate(): array;

    /**
     * Return file or folder stats info.
     */
    public function actionGetInfo(): array;

    /**
     * Read folder and list its content.
     */
    public function actionReadFolder(): array;

    /**
     * Look for files and/or folders that match search string.
     */
    public function actionSeekFolder(): array;

    /**
     * Save data to file after editing.
     */
    public function actionSaveFile(): array;

    /**
     * Rename file or folder.
     */
    public function actionRename(): array;

    /**
     * Copy file or folder.
     */
    public function actionCopy(): array;

    /**
     * Move file or folder.
     * Also move file thumbnail, if it exists, and the destination dir already has a thumbnail dir.
     * If the destination dir does not have a thumbnail dir, it just deletes the thumbnail.
     */
    public function actionMove(): array;

    /**
     * Delete existed file or folder.
     */
    public function actionDelete(): array;

    /**
     * Upload new file.
     */
    public function actionUpload(): array;

    /**
     * Create new folder.
     */
    public function actionAddFolder(): array;

    /**
     * Download file.
     */
    public function actionDownload(): void;

    /**
     * Returns image file.
     *
     * @param bool $thumbnail Whether to generate image thumbnail
     */
    public function actionGetImage(bool $thumbnail): void;

    /**
     * Read and output file contents data.
     */
    public function actionReadFile(): void;

    /**
     * Retrieves storage summarize info.
     */
    public function actionSummarize(): array;

    /**
     * Extracts files and folders from archive.
     */
    public function actionExtract(): array;
}
