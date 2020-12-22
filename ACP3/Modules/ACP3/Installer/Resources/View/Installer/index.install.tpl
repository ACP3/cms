{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tabs-1" data-toggle="tab">{lang t="installer|db_connection_settings"}</a></li>
            <li><a href="#tabs-2" data-toggle="tab">{lang t="installer|admin_account"}</a></li>
            <li><a href="#tabs-3" data-toggle="tab">{lang t="installer|general"}</a></li>
            <li><a href="#tabs-4" data-toggle="tab">{lang t="installer|advanced"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tabs-1" class="tab-pane fade in active">
                <div class="form-group">
                    <label for="db-host" class="col-sm-2 control-label required">{lang t="installer|db_hostname"}</label>

                    <div class="col-sm-10">
                        <input class="form-control"
                               type="text"
                               name="db_host"
                               id="db-host"
                               value="{$form.db_host}"
                               placeholder="{lang t="installer|db_hostname_description"}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="db-user" class="col-sm-2 control-label required">{lang t="installer|db_username"}</label>

                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="db_user" id="db-user" value="{$form.db_user}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="db-password" class="col-sm-2 control-label">{lang t="installer|db_password"}</label>

                    <div class="col-sm-10">
                        <input class="form-control"
                               type="password"
                               name="db_password"
                               id="db-password"
                               placeholder="{lang t="installer|optional"}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="db-name" class="col-sm-2 control-label required">{lang t="installer|db_name"}</label>

                    <div class="col-sm-10">
                        <select class="form-control"
                                name="db_name"
                                id="db-name"
                                required
                                data-available-databases-url="{uri args="installer/index/available_databases"}">
                            <option value="">{lang t="installer|please_select"}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="db-pre" class="col-sm-2 control-label">{lang t="installer|db_table_prefix"}</label>

                    <div class="col-sm-10">
                        <input class="form-control"
                               type="text"
                               name="db_pre"
                               id="db-pre"
                               value="{$form.db_pre}"
                               placeholder="{lang t="installer|optional"}">
                    </div>
                </div>
            </div>
            <div id="tabs-2" class="tab-pane fade">
                <div class="form-group">
                    <label for="user-name" class="col-sm-2 control-label required">{lang t="installer|nickname"}</label>

                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="user_name" id="user-name" value="{$form.user_name}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="user-pwd" class="col-sm-2 control-label required">{lang t="installer|pwd"}</label>

                    <div class="col-sm-10">
                        <input class="form-control" type="password" name="user_pwd" id="user-pwd" required></div>
                </div>
                <div class="form-group">
                    <label for="user-pwd-wdh" class="col-sm-2 control-label required">{lang t="installer|pwd_repeat"}</label>

                    <div class="col-sm-10">
                        <input class="form-control" type="password" name="user_pwd_wdh" id="user-pwd-wdh" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="mail" class="col-sm-2 control-label required">{lang t="installer|email"}</label>

                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="mail" id="mail" value="{$form.mail}" required>
                    </div>
                </div>
            </div>
            <div id="tabs-3" class="tab-pane fade">
                <div class="form-group">
                    <label for="title" class="col-sm-2 control-label required">{lang t="installer|site_title"}</label>

                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="title" id="title" value="{$form.title}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="design" class="col-sm-2 control-label required">{lang t="installer|design"}</label>
                    <div class="col-sm-10">
                        <select name="design" id="design" class="form-control">
                            {foreach $designs as $row}
                                <option value="{$row.dir}"{$row.selected}>{$row.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="date-format-long" class="col-sm-2 control-label required">{lang t="installer|date_format_long"}</label>

                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20" required>

                        <p class="help-block">{lang t="installer|php_date_function"}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="date-format-short" class="col-sm-2 control-label required">{lang t="installer|date_format_short"}</label>

                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="date-time-zone" class="col-sm-2 control-label required">{lang t="installer|time_zone"}</label>

                    <div class="col-sm-10">
                        <select class="form-control" name="date_time_zone" id="date-time-zone" required>
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
            <div id="tabs-4" class="tab-pane fade">
                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <div class="checkbox">
                            <label for="sample-data">
                                <input type="checkbox" name="sample_data" id="sample-data" value="1">
                                {lang t="installer|install_sample_data"}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="installer|submit"}</button>
        </div>
    </div>
    {javascripts}
        {include_js module="system" file="partials/ajax-form"}
        {include_js module="installer" file="partials/available_databases"}
    {/javascripts}
{/block}
