{extends file="asset:layout.tpl"}

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
                <li><a href="#tab-4" data-toggle="tab">{lang t="users|settings"}</a></li>
                <li><a href="#tab-5" data-toggle="tab">{lang t="users|privacy"}</a></li>
                <li><a href="#tab-6" data-toggle="tab">{lang t="users|pwd"}</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade in active">
                    <div class="form-group">
                        <label for="nickname" class="col-sm-2 control-label">{lang t="users|nickname"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="nickname" id="nickname" value="{$form.nickname}" maxlength="30">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="realname" class="col-sm-2 control-label">{lang t="users|realname"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="realname" id="realname" value="{$form.realname}" maxlength="80">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gender" class="col-sm-2 control-label">{lang t="users|gender"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="gender" id="gender">
                                {foreach $gender as $row}
                                    <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date-birthday-input" class="col-sm-2 control-label">{lang t="users|birthday"}</label>

                        <div class="col-sm-10">
                            {datepicker name="birthday" value=$birthday}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="roles" class="col-sm-2 control-label">{lang t="permissions|roles"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="roles[]" id="roles" multiple="multiple" style="height:100px">
                                {foreach $roles as $row}
                                    <option value="{$row.id}"{$row.selected}>{$row.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="super-user-1" class="col-sm-2 control-label">{lang t="users|super_user"}</label>

                        <div class="col-sm-10">
                            <div class="btn-group" data-toggle="buttons">
                                {foreach $super_user as $row}
                                    <label for="super-user-{$row.value}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                        <input type="radio" name="super_user" id="super-user-{$row.value}" value="{$row.value}"{$row.checked}>
                                        {$row.lang}
                                    </label>
                                {/foreach}
                            </div>
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
                            <input class="form-control" type="text" name="zip" id="zip" value="{$form.zip}" maxlength="5"></div>
                    </div>
                    <div class="form-group">
                        <label for="city" class="col-sm-2 control-label">{lang t="users|address_city"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="city" id="city" value="{$form.city}" maxlength="80"></div>
                    </div>
                    <div class="form-group">
                        <label for="country" class="col-sm-2 control-label">{lang t="users|country"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="country" id="country">
                                <option value="">{lang t="system|pls_select"}</option>
                                {foreach $countries as $row}
                                    <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div id="tab-4" class="tab-pane fade">
                    <div class="form-group">
                        <label for="language" class="col-sm-2 control-label">{lang t="users|language"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="language" id="language">
                                <option value="">{lang t="system|pls_select"}</option>
                                {foreach $languages as $row}
                                    <option value="{$row.iso}"{if $row.selected} selected="selected"{/if}>{$row.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="entries" class="col-sm-2 control-label">{lang t="system|records_per_page"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="entries" id="entries">
                                {foreach $entries as $row}
                                    <option value="{$row.value}"{$row.selected}>{$row.value}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date-format-long" class="col-sm-2 control-label">{lang t="system|date_format_long"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20">

                            <p class="help-block">{lang t="system|php_date_function"}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date-format-short" class="col-sm-2 control-label">{lang t="system|date_format_short"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date-time-zone" class="col-sm-2 control-label">{lang t="system|time_zone"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="date_time_zone" id="date-time-zone">
                                {foreach $time_zones as $key => $values}
                                    <optgroup label="{$key}">
                                        {foreach $values as $country => $value}
                                            <option value="{$country}" style="margin:0 0 0 10px"{$value.selected}>{$country}</option>
                                        {/foreach}
                                    </optgroup>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div id="tab-5" class="tab-pane fade">
                    <div class="form-group">
                        <label for="{$mail_display.0.id}" class="col-sm-2 control-label">{lang t="users|display_mail"}</label>

                        <div class="col-sm-10">
                            <div class="btn-group" data-toggle="buttons">
                                {foreach $mail_display as $row}
                                    <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                        <input type="radio" name="mail_display" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                        {$row.lang}
                                    </label>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="{$address_display.0.id}" class="col-sm-2 control-label">{lang t="users|display_address"}</label>

                        <div class="col-sm-10">
                            <div class="btn-group" data-toggle="buttons">
                                {foreach $address_display as $row}
                                    <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                        <input type="radio" name="address_display" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                        {$row.lang}
                                    </label>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="{$country_display.0.id}" class="col-sm-2 control-label">{lang t="users|display_country"}</label>

                        <div class="col-sm-10">
                            <div class="btn-group" data-toggle="buttons">
                                {foreach $country_display as $row}
                                    <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                        <input type="radio" name="country_display" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                        {$row.lang}
                                    </label>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="{$birthday_display.0.id}" class="col-sm-2 control-label">{lang t="users|birthday"}</label>

                        <div class="col-sm-10">
                            {foreach $birthday_display as $row}
                                <div class="radio">
                                    <label for="{$row.id}">
                                        <input type="radio" name="birthday_display" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                        {$row.lang}
                                    </label>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
                <div id="tab-6" class="tab-pane fade">
                    <div class="form-group">
                        <label for="pwd" class="col-sm-2 control-label">{lang t="users|pwd"}</label>

                        <div class="col-sm-10"><input class="form-control" type="password" name="pwd" id="pwd"></div>
                    </div>
                    <div class="form-group">
                        <label for="pwd-repeat" class="col-sm-2 control-label">{lang t="users|pwd_repeat"}</label>

                        <div class="col-sm-10"><input class="form-control" type="password" name="pwd_repeat" id="pwd-repeat"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/users"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="forms"}
    {/javascripts}
{/block}