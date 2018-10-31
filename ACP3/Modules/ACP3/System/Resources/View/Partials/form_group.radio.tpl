{extends file="asset:System/Partials/form_group.base.tpl"}

{$labelSelector='pt-0'}

{block FORM_GROUP_FORM_FIELD}
    {foreach $options as $row}
        <div class="form-check">
            <input type="radio"
                   name="{$row.name}"
                   id="{$row.id}"
                   value="{$row.value}"
                   class="form-check-input"
                    {$row.checked}
                    {if isset($required) && $required === true} required{/if}>
            <label for="{$row.id}" class="form-check-label">
                {$row.lang}
            </label>
        </div>
    {/foreach}
{/block}
