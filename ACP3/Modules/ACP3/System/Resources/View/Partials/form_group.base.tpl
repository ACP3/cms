<div class="form-group">
    {if !empty($label)}
        <label class="col-sm-2 control-label{if (isset($required) && $required === true) || (isset($labelRequired) && $labelRequired === true)} required{/if}"
               {block FORM_GROUP_LABEL_ID}for="{$options.0.id}"{/block}>
            {$label}
        </label>
    {/if}

    <div class="{if empty($label)}col-sm-offset-2 {/if}{if !empty($cssSelector)}{$cssSelector}{else}col-sm-10{/if}">
        {block FORM_GROUP_FORM_FIELD}{/block}
        {if !empty($help)}
            <p class="help-block">{$help}</p>
        {/if}
    </div>
</div>
