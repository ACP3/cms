{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
    <div class="form-group">
        <label for="resource" class="col-lg-2 control-label">{lang t="permissions|resource_name"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="text" name="resource" id="resource" value="{$form.resource}" required>
        </div>
    </div>
    <div class="form-group">
        <label for="privileges" class="col-lg-2 control-label">{lang t="permissions|assigned_privilege"}</label>

        <div class="col-lg-10">
            <select class="form-control" name="privileges" id="privileges">
                {foreach $privileges as $row}
                    <option value="{$row.id}"{$row.selected}>{$row.key}{if !empty($row.description)} ({$row.description}){/if}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <input type="hidden" name="modules" value="{$form.modules}">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/permissions/list_resources"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>