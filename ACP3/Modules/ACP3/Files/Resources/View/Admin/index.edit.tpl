{extends file="asset:Files/Admin/index.create.tpl"}

{block FILES_FILE_UPLOAD}
    <div class="form-group">
        <label class="col-sm-2 control-label">{lang t="files|current_file"}</label>

        <div class="col-sm-10">
            <div class="form-control-static">{$current_file}</div>
        </div>
    </div>
    <div class="form-group">
        <label for="file-internal" class="col-sm-2 control-label">{lang t="files|new_file"}</label>

        <div class="col-sm-10">
            <div class="checkbox">
                <label for="external">
                    <input type="checkbox" name="external" id="external" value="1"{$checked_external}>
                    {lang t="files|external_resource"}
                </label>
            </div>
            <input type="file" name="file_internal" id="file-internal">
            <input class="form-control" type="url" name="file_external" id="file-external" value="" maxlength="120">
        </div>
    </div>
{/block}
