{extends file="asset:System/Partials/form_group.base.tpl"}

{$required=true}
{if !$captcha.input_only}
    {$label={lang t="captcha|captcha"}}
{else}
    {$cssSelector=''}
{/if}

{block FORM_GROUP_LABEL_ID}for="{$captcha.id}"{/block}
{block FORM_GROUP_FORM_FIELD}
    <div class="input-group input-group-lg">
        <span class="input-group-addon" style="padding-top: 5px; padding-bottom: 5px">
            <img src="{uri args="captcha/index/image/path_`$captcha.path`"}"
                 width="{$captcha.width}"
                 height="{$captcha.height}"
                 alt="">
        </span>
        <input class="form-control input-lg"
               type="text"
               name="captcha"
               id="{$captcha.id}"
               value=""
               required>
    </div>
{/block}
