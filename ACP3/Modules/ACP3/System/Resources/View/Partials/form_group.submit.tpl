<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" name="submit" class="btn btn-primary">
            {if !empty($submitLabel)}
                {$submitLabel}
            {else}
                {lang t="system|submit"}
            {/if}
        </button>
        {if !empty($back_url)}
            <a href="{$back_url}" class="btn btn-default">{lang t="system|cancel"}</a>
        {/if}
        {if !empty($form_token)}
            {$form_token}
        {/if}
    </div>
</div>
