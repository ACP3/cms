{if isset($floatingLabel) && $floatingLabel}
    <div class="form-floating mb-3 {if isset($cssSelector)}{$cssSelector}{/if}"
        {if isset($formGroupId)} id="{$formGroupId}"{/if}>
        {block FORM_GROUP_FORM_FIELD}{/block}
        {if !empty($label)}
            <label class="form-label{if (isset($required) && $required === true) || (isset($labelRequired) && $labelRequired === true)} required{/if}{if isset($labelSelectors)} {$labelSelectors}{/if}"
                   {block FORM_GROUP_LABEL_ID}for="{if isset($formFieldId)}{$formFieldId}{elseif !isset($options[0].id)}{$name|replace:'_':'-'}{else}{$options[0].id}{/if}"{/block}>
                {$label}
            </label>
        {/if}
        {block FORM_GROUP_FORM_FIELD_EXTENSION}{/block}
        {if !empty($help)}
            <p class="form-text mb-0">{$help}</p>
        {/if}
    </div>
{else}
    {$formBreakpoint = (isset($formBreakpoint)) ? (empty($formBreakpoint)) ? '' : "-`$formBreakpoint`" : '-md'}

    <div class="row mb-3{if !empty($formGroupSelector)} {$formGroupSelector}{/if}"
        {if isset($formGroupId)} id="{$formGroupId}"{/if}>
        {if !empty($label)}
            <label class="col{$formBreakpoint}-2 col-form-label{if (isset($required) && $required === true) || (isset($labelRequired) && $labelRequired === true)} required{/if}{if isset($labelSelectors)} {$labelSelectors}{/if}"
                   {block FORM_GROUP_LABEL_ID}for="{if isset($formFieldId)}{$formFieldId}{elseif !isset($options[0].id)}{$name|replace:'_':'-'}{else}{$options[0].id}{/if}"{/block}>
                {$label}
            </label>
        {/if}

        {if isset($cssSelector) && empty($cssSelector)}
            <div class="col">
                {block FORM_GROUP_FORM_FIELD_EXTENSION}{/block}
                {block FORM_GROUP_FORM_FIELD}{/block}
                {if !empty($help)}
                    <p class="form-text mb-0">{$help}</p>
                {/if}
            </div>
        {else}
            <div class="{if isset($cssSelector)}{$cssSelector}{else}{if empty($label)}offset{$formBreakpoint}-2 {/if}{if !empty($columnSelector)}{$columnSelector}{else}col{$formBreakpoint}-10{/if}{/if}">
                {block FORM_GROUP_FORM_FIELD_EXTENSION}{/block}
                {block FORM_GROUP_FORM_FIELD}{/block}
                {if !empty($help)}
                    <p class="form-text mb-0">{$help}</p>
                {/if}
            </div>
        {/if}
    </div>
{/if}
