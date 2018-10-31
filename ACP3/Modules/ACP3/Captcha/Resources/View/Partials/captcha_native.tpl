{extends file="asset:System/Partials/form_group.base.tpl"}

{$required=true}
{if !$captcha.input_only}
    {$labelSelector='mt-auto'}
    {$label={lang t="captcha|captcha"}}
{else}
    {$cssSelector=''}
    {$formGroupSelector=''}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$captcha.id}"{/block}
{block FORM_GROUP_FORM_FIELD}
    <img src="{uri args="captcha/index/image/path_`$captcha.path`"}"
         width="{$captcha.width}"
         height="{$captcha.height}"
         alt="">
    <input class="form-control mt-2"
           type="text"
           name="captcha"
           id="{$captcha.id}"
           value=""
           required>
{/block}
