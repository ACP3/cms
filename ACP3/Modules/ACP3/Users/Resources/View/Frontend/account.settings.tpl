{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="tabbable">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a href="#tab-privacy" class="nav-link active" data-toggle="tab">{lang t="users|privacy"}</a></li>
            <li class="nav-item"><a href="#tab-password" class="nav-link" data-toggle="tab">{lang t="users|pwd"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-privacy" class="tab-pane fade show active">
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$mail_display required=true label={lang t="users|display_mail"}}
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$address_display required=true label={lang t="users|display_address"}}
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$country_display required=true label={lang t="users|display_country"}}
                {include file="asset:System/Partials/form_group.radio.tpl" options=$birthday_display required=true label={lang t="users|birthday"}}
            </div>
            <div id="tab-password" class="tab-pane fade">
                {include file="asset:Users/Partials/password_fields.tpl" field_name='new_pwd' translator_phrase="new_pwd"}
            </div>
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/users/account"}}
{/block}
