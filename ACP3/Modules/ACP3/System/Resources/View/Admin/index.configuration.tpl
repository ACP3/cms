{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {redirect_message}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general"}</a></li>
                <li><a href="#tab-2" data-toggle="tab">{lang t="system|date"}</a></li>
                <li><a href="#tab-3" data-toggle="tab">{lang t="system|maintenance"}</a></li>
                <li><a href="#tab-5" data-toggle="tab">{lang t="system|performance"}</a></li>
                <li><a href="#tab-6" data-toggle="tab">{lang t="system|email"}</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade in active">
                    <div class="form-group">
                        <label for="homepage" class="col-sm-2 control-label required">{lang t="system|homepage"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="homepage" id="homepage" value="{$form.homepage}" required>

                            <p class="help-block">{lang t="system|homepage_description"}</p>
                        </div>
                    </div>
                    {include file="asset:System/Partials/form_group.select.tpl" options=$entries required=true label={lang t="system|records_per_page"}}
                    <div class="form-group">
                        <label for="flood" class="col-sm-2 control-label required">{lang t="system|flood_barrier"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="number" name="flood" id="flood" value="{$form.flood}" min="0" required>

                            <p class="help-block">{lang t="system|flood_barrier_description"}</p>
                        </div>
                    </div>
                    {include file="asset:System/Partials/form_group.select.tpl" options=$wysiwyg required=true label={lang t="system|editor"}}
                    <div class="form-group">
                        <label for="language" class="col-sm-2 control-label required">{lang t="system|language"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="language" id="language" required>
                                <option value="">{lang t="system|pls_select"}</option>
                                {foreach $languages as $row}
                                    <option value="{$row.iso}"{if $row.selected} selected="selected"{/if}>{$row.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="{$mod_rewrite.0.id}" class="col-sm-2 control-label required">{lang t="system|mod_rewrite"}</label>

                        <div class="col-sm-10">
                            <div class="btn-group" data-toggle="buttons">
                                {foreach $mod_rewrite as $row}
                                    <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                        <input type="radio" name="{$row.name}" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                        {$row.lang}
                                    </label>
                                {/foreach}
                            </div>
                            <p class="help-block">{lang t="system|mod_rewrite_description"}</p>
                        </div>
                    </div>
                </div>
                <div id="tab-2" class="tab-pane fade">
                    <div class="form-group">
                        <label for="date-format-long" class="col-sm-2 control-label required">{lang t="system|date_format_long"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="date_format_long" id="date-format-long" value="{$form.date_format_long}" maxlength="20" required>

                            <p class="help-block">{lang t="system|php_date_function"}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date-format-short" class="col-sm-2 control-label required">{lang t="system|date_format_short"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="date_format_short" id="date-format-short" value="{$form.date_format_short}" maxlength="20" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date-time-zone" class="col-sm-2 control-label required">{lang t="system|time_zone"}</label>

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
                <div id="tab-3" class="tab-pane fade">
                    <div class="form-group">
                        <label for="{$maintenance.0.id}" class="col-sm-2 control-label required">{lang t="system|maintenance_mode"}</label>

                        <div class="col-sm-10">
                            <div class="btn-group" data-toggle="buttons">
                                {foreach $maintenance as $row}
                                    <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                        <input type="radio" name="{$row.name}" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                        {$row.lang}
                                    </label>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="maintenance-message" class="col-sm-2 control-label required">{lang t="system|maintenance_msg"}</label>

                        <div class="col-sm-10">
                            <textarea class="form-control" name="maintenance_message" id="maintenance-message" cols="50" rows="6" required>{$form.maintenance_message}</textarea>
                        </div>
                    </div>
                </div>
                <div id="tab-5" class="tab-pane fade">
                    <div class="form-group">
                        <label for="{$cache_images.0.id}" class="col-sm-2 control-label required">{lang t="system|cache_images"}</label>

                        <div class="col-sm-10">
                            <div class="btn-group" data-toggle="buttons">
                                {foreach $cache_images as $row}
                                    <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                        <input type="radio" name="{$row.name}" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                        {$row.lang}
                                    </label>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cache-lifetime" class="col-sm-2 control-label required">{lang t="system|cache_lifetime"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="cache_lifetime" id="cache-lifetime" value="{$form.cache_lifetime}" maxlength="20" required>

                            <p class="help-block">{lang t="system|cache_lifetime_description"}</p>
                        </div>
                    </div>
                </div>
                <div id="tab-6" class="tab-pane fade">
                    {include file="asset:System/Partials/form_group.select.tpl" options=$mailer_type required=true label={lang t="system|mailer_type"}}
                    <div id="mailer-smtp-1">
                        <div class="form-group">
                            <label for="mailer-smtp-host" class="col-sm-2 control-label required">{lang t="system|mailer_smtp_hostname"}</label>

                            <div class="col-sm-10">
                                <input class="form-control" type="text" name="mailer_smtp_host" id="mailer-smtp-host" value="{$form.mailer_smtp_host}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mailer-smtp-port" class="col-sm-2 control-label required">{lang t="system|mailer_smtp_port"}</label>

                            <div class="col-sm-10">
                                <input class="form-control" type="number" name="mailer_smtp_port" id="mailer-smtp-port" value="{$form.mailer_smtp_port}">
                            </div>
                        </div>
                        {include file="asset:System/Partials/form_group.select.tpl" options=$mailer_smtp_security required=true label={lang t="system|mailer_smtp_security"}}
                        <div class="form-group">
                            <label for="{$mailer_smtp_auth.0.id}" class="col-sm-2 control-label required">{lang t="system|mailer_smtp_auth"}</label>

                            <div class="col-sm-10">
                                <div class="btn-group" data-toggle="buttons">
                                    {foreach $mailer_smtp_auth as $row}
                                        <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                            <input type="radio" name="{$row.name}" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                            {$row.lang}
                                        </label>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                        <div id="mailer-smtp-2">
                            <div class="form-group">
                                <label for="mailer-smtp-user" class="col-sm-2 control-label required">{lang t="system|mailer_smtp_username"}</label>

                                <div class="col-sm-10">
                                    <input class="form-control" type="text" name="mailer_smtp_user" id="mailer-smtp-user" value="{$form.mailer_smtp_user}" maxlength="40">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="mailer-smtp-password" class="col-sm-2 control-label">{lang t="system|mailer_smtp_password"}</label>

                                <div class="col-sm-10">
                                    <input class="form-control" type="password" name="mailer_smtp_password" id="mailer-smtp-password" value="{$form.mailer_smtp_password}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token}
    </form>
    {javascripts}
        {include_js module="system" file="admin/index.configuration"}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
