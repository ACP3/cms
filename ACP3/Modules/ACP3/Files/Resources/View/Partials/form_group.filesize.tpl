{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    <div class="row">
        <div class="input-group flex-nowrap">
            <input {if isset($use_form_control) && $use_form_control === false}{else}class="{if (isset($readonly) && $readonly === true) || (isset($disabled) && $disabled === true)}form-control-plaintext{else}form-control{/if}"{/if}
                   type="number"
                   name="{$name}"
                   id="{$name|replace:'_':'-'}"
                   {if isset($value)}value="{$value}"{/if}
                   maxlength="15"
                   min="0">
            <select class="form-select" name="unit" id="unit" aria-label="{lang t="files|unit"}">
                {foreach $units as $row}
                    <option value="{$row.value}"{$row.selected}>{$row.value}</option>
                {/foreach}
            </select>
        </div>
    </div>
{/block}
