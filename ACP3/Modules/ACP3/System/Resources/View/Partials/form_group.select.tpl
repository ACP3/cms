{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    <select class="form-control"
            name="{$options.0.name}"
            id="{$options.0.id}"
            {if isset($required) && $required === true} required size="{count($options)}"{/if}
            {if isset($disabled) && $disabled === true} disabled{/if}>
        {if !isset($required) || $required === false}
            <option value="">
                {if !empty($emptyOptionLabel)}
                    {$emptyOptionLabel}
                {else}
                    {lang t="system|pls_select"}
                {/if}
            </option>
        {/if}
        {foreach $options as $row}
            <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
        {/foreach}
    </select>
{/block}
