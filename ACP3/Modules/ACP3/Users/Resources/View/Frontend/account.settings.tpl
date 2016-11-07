{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-localization" data-toggle="tab">{lang t="users|localization"}</a></li>
            <li><a href="#tab-privacy" data-toggle="tab">{lang t="users|privacy"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-localization" class="tab-pane fade in active">
                <div class="form-group">
                    <label for="language" class="col-sm-2 control-label required">{lang t="users|language"}</label>

                    <div class="col-sm-10">
                        <select class="form-control" name="language" id="language" size="{count($languages)}" required{if $language_override == 0} disabled{/if}>
                            {foreach $languages as $row}
                                <option value="{$row.iso}"{if $row.selected} selected="selected"{/if}>{$row.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                {include file="asset:System/Partials/form_group.input_text.tpl" name="date_format_long" value=$form.date_format_long required=true maxlength=20 label={lang t="system|date_format_long"} help={lang t="system|php_date_function"}}
                {include file="asset:System/Partials/form_group.input_text.tpl" name="date_format_short" value=$form.date_format_short required=true maxlength=20 label={lang t="system|date_format_short"} help={lang t="system|php_date_function"}}
                <div class="form-group">
                    <label for="date-time-zone" class="col-sm-2 control-label required">{lang t="system|time_zone"}</label>

                    <div class="col-sm-10">
                        <select class="form-control" name="date_time_zone" id="date-time-zone">
                            {foreach $time_zones as $continent => $countries}
                                <optgroup label="{$continent}">
                                    {foreach $countries as $country => $data}
                                        <option value="{$country}"{$data.selected}>{$country}</option>
                                    {/foreach}
                                </optgroup>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div id="tab-privacy" class="tab-pane fade">
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$mail_display required=true label={lang t="users|display_mail"}}
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$address_display required=true label={lang t="users|display_address"}}
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$country_display required=true label={lang t="users|display_country"}}
                {include file="asset:System/Partials/form_group.radio.tpl" options=$birthday_display required=true label={lang t="users|birthday"}}
            </div>
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/users/account"}}
{/block}
