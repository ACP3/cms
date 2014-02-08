{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="system|date"}</a></li>
            <li><a href="#tab-3" data-toggle="tab">{lang t="users|privacy"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                <div class="form-group">
                    <label for="language" class="col-lg-2 control-label">{lang t="users|language"}</label>

                    <div class="col-lg-10">
                        <select class="form-control" name="language" id="language"{if $language_override == 0} disabled{/if}>
                            <option value="">{lang t="system|pls_select"}</option>
                            {foreach $languages as $row}
                                <option value="{$row.dir}"{$row.selected}>{$row.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="entries" class="col-lg-2 control-label">{lang t="system|records_per_page"}</label>

                    <div class="col-lg-10">
                        <select class="form-control" name="entries" id="entries"{if $entries_override == 0} disabled{/if}>
                            {foreach $entries as $row}
                                <option value="{$row.value}"{$row.selected}>{$row.value}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div id="tab-2" class="tab-pane fade">
                <div class="form-group">
                    <label for="date-format-long" class="col-lg-2 control-label">{lang t="system|date_format_long"}</label>

                    <div class="col-lg-10">
                        <input class="form-control" type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20">

                        <p class="help-block">{lang t="system|php_date_function"}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="date-format-short" class="col-lg-2 control-label">{lang t="system|date_format_short"}</label>

                    <div class="col-lg-10">
                        <input class="form-control" type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20">
                    </div>
                </div>
                <div class="form-group">
                    <label for="date-time-zone" class="col-lg-2 control-label">{lang t="system|time_zone"}</label>

                    <div class="col-lg-10">
                        <select class="form-control" name="date_time_zone" id="date-time-zone">
                            {foreach $time_zones as $key => $values}
                                <optgroup label="{$key}">
                                    {foreach $values as $country => $value}
                                        <option value="{$country}"{$value.selected}>{$country}</option>
                                    {/foreach}
                                </optgroup>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div id="tab-3" class="tab-pane fade">
                <div class="form-group">
                    <label for="{$mail_display.0.id}" class="col-lg-2 control-label">{lang t="users|display_mail"}</label>

                    <div class="col-lg-10">
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
                    <label for="{$address_display.0.id}" class="col-lg-2 control-label">{lang t="users|display_address"}</label>

                    <div class="col-lg-10">
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
                    <label for="{$country_display.0.id}" class="col-lg-2 control-label">{lang t="users|display_country"}</label>

                    <div class="col-lg-10">
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
                    <label for="{$birthday_display.0.id}" class="col-lg-2 control-label">{lang t="users|birthday"}</label>

                    <div class="col-lg-10">
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
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="users/home"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>