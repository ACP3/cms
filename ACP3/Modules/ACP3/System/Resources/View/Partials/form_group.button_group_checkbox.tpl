{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    <div class="btn-group" role="group">
        {foreach $options as $row}
            <input type="checkbox"
                   class="btn-check"
                   name="{$row.name}[]"
                   id="{$row.id}"
                   value="{$row.value}"
                   autocomplete="off"
                    {if isset($required) && $required === true} required{/if}
                    {$row.checked}>
            <label for="{$row.id}" class="btn btn-light">
                {$row.lang}
            </label>
        {/foreach}
    </div>
{/block}
