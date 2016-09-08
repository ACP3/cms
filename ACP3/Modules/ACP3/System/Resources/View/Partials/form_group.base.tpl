<div class="form-group">
    <label for="{$options.0.id}"
           class="col-sm-2 control-label{if (isset($required) && $required === true) || (isset($labelRequired) && $labelRequired === true)} required{/if}">
        {$label}
    </label>

    <div class="col-sm-10">
        {block FORM_GROUP_FORM_FIELD}{/block}
        {if !empty($help)}
            <p class="help-block">{$help}</p>
        {/if}
    </div>
</div>
