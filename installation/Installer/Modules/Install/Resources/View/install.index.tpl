{extends file="asset:`$LAYOUT`"}

{block CONTENT prepend}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{uri args="install/install"}"
          method="post"
          accept-charset="UTF-8"
          class="form-horizontal"
          id="config-form"
          data-available-databases-url="{uri args="install/install/available_databases"}"
          data-ajax-form="true"
          data-ajax-form-loading-text="{lang t="install|loading_please_wait"}">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tabs-1" data-toggle="tab">{lang t="install|db_connection_settings"}</a></li>
                <li><a href="#tabs-2" data-toggle="tab">{lang t="install|admin_account"}</a></li>
                <li><a href="#tabs-3" data-toggle="tab">{lang t="install|general"}</a></li>
            </ul>
            <div class="tab-content">
                <div id="tabs-1" class="tab-pane fade in active">
                    <div class="form-group">
                        <label for="db-host" class="col-sm-2 control-label required">{lang t="install|db_hostname"}</label>

                        <div class="col-sm-10">
                            <input class="form-control"
                                   type="text"
                                   name="db_host"
                                   id="db-host"
                                   value="{$form.db_host}"
                                   placeholder="{lang t="install|db_hostname_description"}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="db-user" class="col-sm-2 control-label required">{lang t="install|db_username"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="db_user" id="db-user" value="{$form.db_user}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="db-password" class="col-sm-2 control-label">{lang t="install|db_password"}</label>

                        <div class="col-sm-10">
                            <input class="form-control"
                                   type="password"
                                   name="db_password"
                                   id="db-password"
                                   placeholder="{lang t="install|optional"}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="db-name" class="col-sm-2 control-label required">{lang t="install|db_name"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="db_name" id="db-name" required>
                                <option value="">{lang t="install|please_select"}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="db-pre" class="col-sm-2 control-label">{lang t="install|db_table_prefix"}</label>

                        <div class="col-sm-10">
                            <input class="form-control"
                                   type="text"
                                   name="db_pre"
                                   id="db-pre"
                                   value="{$form.db_pre}"
                                   placeholder="{lang t="install|optional"}">
                        </div>
                    </div>
                </div>
                <div id="tabs-2" class="tab-pane fade">
                    <div class="form-group">
                        <label for="user-name" class="col-sm-2 control-label required">{lang t="install|nickname"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="user_name" id="user-name" value="{$form.user_name}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="user-pwd" class="col-sm-2 control-label required">{lang t="install|pwd"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="password" name="user_pwd" id="user-pwd" required></div>
                    </div>
                    <div class="form-group">
                        <label for="user-pwd-wdh" class="col-sm-2 control-label required">{lang t="install|pwd_repeat"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="password" name="user_pwd_wdh" id="user-pwd-wdh" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mail" class="col-sm-2 control-label required">{lang t="install|email"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="mail" id="mail" value="{$form.mail}" required>
                        </div>
                    </div>
                </div>
                <div id="tabs-3" class="tab-pane fade">
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label required">{lang t="install|site_title"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="title" id="title" value="{$form.title}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="design" class="col-sm-2 control-label required">{lang t="install|design"}</label>
                        <div class="col-sm-10">
                            <select name="design" id="design" class="form-control">
                                {foreach $designs as $row}
                                    <option value="{$row.dir}"{$row.selected}>{$row.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date-format-long" class="col-sm-2 control-label required">{lang t="install|date_format_long"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20" required>

                            <p class="help-block">{lang t="install|php_date_function"}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date-format-short" class="col-sm-2 control-label required">{lang t="install|date_format_short"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date-time-zone" class="col-sm-2 control-label required">{lang t="install|time_zone"}</label>

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
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="install|submit"}</button>
            </div>
        </div>
    </form>
    {javascripts}
        <script type="text/javascript" src="{$ROOT_DIR}ACP3/Modules/ACP3/System/Resources/Assets/js/ajax-form.js"></script>
        <script type="text/javascript" src="{$INSTALLER_ROOT_DIR}Installer/Modules/Install/Resources/Assets/js/available_databases.js"></script>
    {/javascripts}
{/block}
