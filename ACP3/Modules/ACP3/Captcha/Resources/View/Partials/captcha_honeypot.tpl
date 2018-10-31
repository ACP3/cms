{extends file="asset:System/Partials/form_group.base.tpl"}

{$formGroupSelector='d-none'}
{if $captcha.input_only}
    {$cssSelector=''}
{else}
    {$label={lang t="captcha|do_not_fill_out"}}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$captcha.id}"{/block}
{block FORM_GROUP_FORM_FIELD}
    <input class="form-control"
           type="text"
           name="captcha"
           id="{$captcha.id}"
           value="">
{/block}
