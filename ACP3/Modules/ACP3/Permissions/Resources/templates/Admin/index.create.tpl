{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general_statements"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="permissions|permissions"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {include file="asset:System/Partials/form_group.input_text.tpl" name="name" value=$form.name required=true maxlength=100 label={lang t="system|name"}}
                {if !empty($parent)}
                    <div class="form-group">
                        <label for="parent-id" class="col-sm-2 control-label required">{lang t="permissions|superior_role"}</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="parent_id" id="parent-id" required>
                                {foreach $parent as $row}
                                    <option value="{$row.id}"{$row.selected}>{$row.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                {/if}
            </div>
            <div id="tab-2" class="tab-pane fade">
                {foreach $modules as $module => $values}
                    {if $values@iteration % 2 !== 0}
                        <div class="row">
                    {/if}
                    <fieldset class="col-sm-6">
                        <legend>{$module}</legend>
                        {foreach $values.privileges as $privilege}
                            <div class="form-group">
                                <label class="col-sm-2 control-label"{if !empty($privilege.description)} title="{$privilege.description}"{/if}>{$privilege.key}</label>

                                <div class="col-sm-10">
                                    <div class="btn-group" data-toggle="buttons">
                                        {foreach $privilege.select as $row}
                                            <label for="privileges-{$values.id}-{$privilege.id}-{$row.value}" class="btn btn-default{if !empty($row.selected)} active{/if}">
                                                <input type="radio" name="privileges[{$values.id}][{$privilege.id}]" id="privileges-{$values.id}-{$privilege.id}-{$row.value}" value="{$row.value}"{$row.selected}>
                                                {$row.lang}
                                                {if $row.value === 2 && isset($privilege.calculated)}
                                                    <small>({$privilege.calculated})</small>
                                                {/if}
                                            </label>
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    </fieldset>
                    {if $values@iteration % 2 === 0 || $values@last}
                        </div>
                    {/if}
                {/foreach}
            </div>
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/permissions"}}
{/block}
