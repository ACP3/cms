{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    <div class="btn-group" data-toggle="buttons">
        {foreach $options as $row}
            <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                <input type="radio"
                       name="{$row.name}"
                       id="{$row.id}"
                       value="{$row.value}"
                        {if isset($required) && $required === true} required{/if}
                        {$row.checked}>
                {$row.lang}
            </label>
        {/foreach}
    </div>
{/block}
