{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a href="#tab-1" class="nav-link active" data-toggle="tab">{lang t="system|general_statements"}</a></li>
        <li class="nav-item"><a href="#tab-2" class="nav-link" data-toggle="tab">{lang t="permissions|permissions"}</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane fade show active">
            {include file="asset:System/Partials/form_group.input_text.tpl" name="name" value=$form.name required=true maxlength=100 label={lang t="system|name"}}
            {if !empty($parent)}
                <div class="form-group row">
                    <label for="parent-id" class="col-sm-2 col-form-label required">{lang t="permissions|superior_role"}</label>
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
                    <legend>{lang t="`$module`|`$module`"}</legend>
                    {foreach $values.privileges as $privilege}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"{if !empty($privilege.description)} title="{$privilege.description}"{/if}>{$privilege.key}</label>

                            <div class="col-sm-10">
                                <div class="btn-group" data-toggle="buttons">
                                    {foreach $privilege.select as $row}
                                        <label for="privileges-{$values.id}-{$privilege.id}-{$row.value}" class="btn btn-light{if !empty($row.selected)} active{/if}">
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
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/permissions"}}
{/block}
