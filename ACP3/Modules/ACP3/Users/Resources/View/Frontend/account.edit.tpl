{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general"}</a></li>
                <li><a href="#tab-2" data-toggle="tab">{lang t="users|contact"}</a></li>
                <li><a href="#tab-3" data-toggle="tab">{lang t="users|address"}</a></li>
                <li><a href="#tab-4" data-toggle="tab">{lang t="users|pwd"}</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade in active">
                    <div class="form-group">
                        <label for="nickname" class="col-sm-2 control-label required">{lang t="users|nickname"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="nickname" id="nickname" value="{$form.nickname}" maxlength="30" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="realname" class="col-sm-2 control-label">{lang t="users|realname"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="realname" id="realname" value="{$form.realname}" maxlength="80">
                        </div>
                    </div>
                    {include file="asset:System/Partials/form_group.select.tpl" options=$gender required=true label={lang t="users|gender"}}
                    <div class="form-group">
                        <label for="date-birthday-input" class="col-sm-2 control-label">{lang t="users|birthday"}</label>

                        <div class="col-sm-10">
                            {datepicker name="birthday" value=$birthday inputFieldOnly=true withTime=false}
                        </div>
                    </div>
                </div>
                <div id="tab-2" class="tab-pane fade">
                    {foreach $contact as $row}
                        <div class="form-group">
                            <label for="{$row.name}" class="col-sm-2 control-label">{$row.lang}</label>

                            <div class="col-sm-10">
                                <input class="form-control" type="text" name="{$row.name}" id="{$row.name}" value="{$row.value}" maxlength="{$row.maxlength}">
                            </div>
                        </div>
                    {/foreach}
                </div>
                <div id="tab-3" class="tab-pane fade">
                    <div class="form-group">
                        <label for="street" class="col-sm-2 control-label">{lang t="users|address_street"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="street" id="street" value="{$form.street}" maxlength="80">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="house-number" class="col-sm-2 control-label">{lang t="users|address_house_number"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="house_number" id="house-number" value="{$form.house_number}" maxlength="5">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="zip" class="col-sm-2 control-label">{lang t="users|address_zip"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="zip" id="zip" value="{$form.zip}" maxlength="5">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="city" class="col-sm-2 control-label">{lang t="users|address_city"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="city" id="city" value="{$form.city}" maxlength="80">
                        </div>
                    </div>
                    {include file="asset:System/Partials/form_group.select.tpl" options=$countries label={lang t="users|country"}}
                </div>
                <div id="tab-4" class="tab-pane fade">
                    {include file="asset:Users/Partials/password_fields.tpl" field_name='new_pwd' translator_phrase="new_pwd"}
                </div>
            </div>
        </div>
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/users/account"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
