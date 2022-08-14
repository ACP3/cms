{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="installer-install-form"}
        {tab title={lang t="installer|db_connection_settings"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="db_host" value=$form.db_host required=true label={lang t="installer|db_hostname"} attributes=['placeholder' => {lang t="installer|db_hostname_description"}]}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="db_user" value=$form.db_user required=true label={lang t="installer|db_username"}}
            {include file="asset:System/Partials/form_group.input_password.tpl" name="db_password" value='' label={lang t="installer|db_password"} attributes=['placeholder' => {lang t="installer|optional"}]}
            {include file="asset:System/Partials/form_group.select.tpl" name="db_name" required=true label={lang t="installer|db_name"} data_attributes=['available-databases-url' => {uri args="installer/index/available_databases"}]}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="db_pre" value=$form.db_pre label={lang t="installer|db_table_prefix"} attributes=['placeholder' => {lang t="installer|optional"}]}
        {/tab}
        {tab title={lang t="installer|admin_account"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="user_name" value=$form.db_pre required=true label={lang t="installer|nickname"}}
            {include file="asset:System/Partials/form_group.input_password.tpl" name="user_pwd" value='' required=true label={lang t="installer|pwd"}}
            {include file="asset:System/Partials/form_group.input_password.tpl" name="user_pwd_wdh" value='' required=true label={lang t="installer|pwd_repeat"}}
            {include file="asset:System/Partials/form_group.input_email.tpl" name="mail" value=$form.mail required=true label={lang t="installer|email"}}
        {/tab}
        {tab title={lang t="installer|general"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true label={lang t="installer|site_title"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$designs required=true label={lang t="installer|design"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="date_format_long" value=$form.date_format_long required=true maxlength=20 label={lang t="installer|date_format_long"} help={lang t="installer|php_date_function"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="date_format_short" value=$form.date_format_short required=true maxlength=20 label={lang t="installer|date_format_short"} help={lang t="installer|php_date_function"}}
            <div class="row mb-3">
                <label for="date-time-zone" class="col-md-2 col-form-label required">{lang t="installer|time_zone"}</label>

                <div class="col-md-10">
                    <select class="form-select" name="date_time_zone" id="date-time-zone" required>
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
        {/tab}
        {tab title={lang t="installer|advanced"}}
            {include file="asset:System/Partials/form_group.checkbox.tpl" options=[['name' => 'sample_data', 'id' => 'sample-data', 'value' => 1, 'checked' => '', 'lang' => {lang t="installer|install_sample_data"}]]}
        {/tab}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit.tpl"}
    {javascripts}
        {include_js module="installer" file="partials/available_databases"}
    {/javascripts}
{/block}
