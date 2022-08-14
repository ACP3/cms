{extends file="asset:System/Partials/form_group.select.tpl"}

{block FORM_GROUP_FORM_FIELD_SELECT_OPTIONS}
    {foreach $menuItems as $blocks}
        <optgroup label="{$blocks.title}">
            {foreach $blocks.items as $row}
                <option value="{$row.id}"{$row.selected}>{$row.spaces}{$row.title}</option>
            {/foreach}
        </optgroup>
    {/foreach}
{/block}
