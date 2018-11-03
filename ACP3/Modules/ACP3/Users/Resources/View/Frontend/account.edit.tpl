{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a href="#tab-1" class="nav-link active" data-toggle="tab">{lang t="system|general"}</a></li>
        <li class="nav-item"><a href="#tab-2" class="nav-link" data-toggle="tab">{lang t="users|contact"}</a></li>
        <li class="nav-item"><a href="#tab-3" class="nav-link" data-toggle="tab">{lang t="users|address"}</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane fade show active">
            {include file="asset:System/Partials/form_group.input_text.tpl" name="nickname" value=$form.nickname required=true maxlength=30 label={lang t="users|nickname"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="realname" value=$form.realname maxlength=80 label={lang t="users|realname"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$gender required=true label={lang t="users|gender"}}
            {datepicker name="birthday" value=$birthday inputFieldOnly=true withTime=false label={lang t="users|birthday"}}
        </div>
        <div id="tab-2" class="tab-pane fade">
            {foreach $contact as $row}
                {include file="asset:System/Partials/form_group.input_text.tpl" name=$row.name value=$row.value maxlength=$row.maxlength label=$row.lang}
            {/foreach}
        </div>
        <div id="tab-3" class="tab-pane fade">
            {include file="asset:System/Partials/form_group.input_text.tpl" name="street" value=$form.street maxlength=80 label={lang t="users|address_street"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="house_number" value=$form.house_number maxlength=5 label={lang t="users|address_house_number"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="zip" value=$form.zip maxlength=5 label={lang t="users|address_zip"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="city" value=$form.city maxlength=80 label={lang t="users|address_city"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$countries label={lang t="users|country"}}
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/users/account"}}
{/block}
