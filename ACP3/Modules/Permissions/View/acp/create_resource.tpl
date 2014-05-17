{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
    <div class="form-group">
        <label for="modules" class="col-lg-2 control-label">{lang t="system|module"}</label>

        <div class="col-lg-10">
            <select class="form-control" name="modules" id="modules">
                {foreach $modules as $row}
                    <option value="{$row.dir}"{$row.selected}>{$row.name}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="area" class="col-lg-2 control-label">{lang t="permissions|area"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="text" name="area" id="area" value="{$form.area}" required>
        </div>
    </div>
    <div class="form-group">
        <label for="controller" class="col-lg-2 control-label">{lang t="permissions|controller"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="text" name="controller" id="controller" value="{$form.controller}" required>
        </div>
    </div>
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
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/permissions/list_resouces"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>
{include_js module="system" file="forms"}